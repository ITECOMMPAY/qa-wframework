<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\NotLikeBeforeExplanation;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Get\GetScreenshot;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class LikeBefore extends AbstractCondition
{
    /** @var string */
    protected $suffix;

    /** @var bool|int */
    protected $default;

    /** @var null */
    protected $waitClosure;

    /** @var string */
    public $screenshot;

    /** @var \Imagick */
    public $diff;

    public function getName() : string
    {
        return "выглядит, как сохранённый эталон: $this->suffix?";
    }

    public function __construct(string $suffix = 'default', $default = true, $waitClosure = null)
    {
        $this->suffix = $suffix;
        $this->waitClosure = $waitClosure;
        $this->default = $default;
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $this->screenshot = $pageObject->accept(new GetScreenshot('', $this->waitClosure));

        $name = $pageObject . '_' . $this->suffix;

        $shotRun = (bool) TestProperties::getValue('shotRun', true);

        if ($shotRun)
        {
            WLogger::logDebug($this, 'сохраняем эталон: ' . $this->suffix);

            $pageObject->returnCodeceptionActor()->putTempShot($name, $this->screenshot);

            if (is_int($this->default))
            {
                usleep($this->default);
                return true;
            }

            return $this->default;
        }

        $reference = $pageObject->returnCodeceptionActor()->getShot($name);

        $similar = $this->compareImages($reference, $this->screenshot);

        if (!$similar)
        {
            $pageObject->returnCodeceptionActor()->putTempShot($name, $this->screenshot);
        }

        return $similar;
    }

    /**
     * Сравнивает две картинки с помощью вызова метода ImageMagick compareImages (METRIC_MEANSQUAREERROR).
     *
     * Если отклонение одной картинки от другой равняется 0 или меньше параметра фреймворка "maxDeviation"
     * то будет возвращён true, иначе - false.
     *
     * @param string $referenceImage
     * @param string $newImage
     * @return bool
     * @throws UsageException
     * @throws \ImagickException
     */
    protected function compareImages(string $referenceImage, string $newImage) : bool
    {
        WLogger::logDebug($this, 'Сравниваем две картинки');

        $imagick1 = new \Imagick();
        $imagick1->readImageBlob($referenceImage);
        $imagick2 = new \Imagick();
        $imagick2->readImageBlob($newImage);

        $imagick1Size = $imagick1->getImageGeometry();
        $imagick2Size = $imagick2->getImageGeometry();

        if ($imagick1Size['width'] !== $imagick2Size['width'] && $imagick1Size['height'] !== $imagick2Size['height'])
        {
            $minWidth = min($imagick1Size['width'], $imagick2Size['width']);
            $minHeight = min($imagick1Size['height'], $imagick2Size['height']);

            $imagick1->resizeImage($minWidth, $minHeight, \Imagick::FILTER_SINC, 1.0);
            $imagick2->resizeImage($minWidth, $minHeight, \Imagick::FILTER_SINC, 1.0);
        }

        try
        {
            [$diff, $deviation] = $imagick1->compareImages($imagick2, \Imagick::METRIC_MEANSQUAREERROR);
            $diff->setImageFormat('png');
            $deviation = (int) (10000 * $deviation);
        }
        catch (\ImagickException $e)
        {
            throw new UsageException("Не получилось сравнить скриншоты: " . PHP_EOL . $e->getMessage());
        }

        $this->diff = $diff;

        $maxDeviation = (int) TestProperties::getValue('maxDeviation');

        if ($deviation <= $maxDeviation)
        {
            WLogger::logDebug($this, "Картинки - одинаковые ($deviation <= $maxDeviation)");

            return true;
        }

        WLogger::logDebug($this, "Картинки - разные ($deviation > $maxDeviation)");

        return false;
    }

    public function getExplanationClasses() : array
    {
        return [NotLikeBeforeExplanation::class];
    }
}