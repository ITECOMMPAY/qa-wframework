<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Get\GetScreenshot;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class LikeBefore extends AbstractCondition
{
    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var null
     */
    protected $waitClosure;

    public $screenshot;

    public $diff;

    public function getName() : string
    {
        return "выглядит, как сохранённый эталон: $this->suffix?";
    }

    public function __construct(string $suffix = 'default', $waitClosure = null)
    {
        $this->suffix = $suffix;
        $this->waitClosure = $waitClosure;
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $this->screenshot = $pageObject->accept(new GetScreenshot('', $this->waitClosure));

        $name = $pageObject . '_' . $this->suffix;

        $reference = $pageObject->returnCodeceptionActor()->getShot($name);

        return $this->compareImages($reference, $this->screenshot);
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
        WLogger::logDebug('Сравниваем две картинки');

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
            WLogger::logDebug("Картинки - одинаковые ($deviation <= $maxDeviation)");

            return true;
        }

        WLogger::logDebug("Картинки - разные ($deviation > $maxDeviation)");

        return false;
    }
}