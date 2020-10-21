<?php


namespace Common\Module\WFramework\WebObjects\SelfieShooter;


use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\Helpers\Rect;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IHiddenOnShot;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Common\Module\WFramework\WebObjects\Base\Interfaces\IResetOnShot;
use Common\Module\WFramework\WebObjects\Base\WPageObject;
use Common\Module\WFramework\WebObjects\SelfieShooter\ComparisonResult\Diff;
use Common\Module\WFramework\WebObjects\SelfieShooter\ComparisonResult\IComparisonResult;
use Common\Module\WFramework\WebObjects\SelfieShooter\ComparisonResult\Same;
use Facebook\WebDriver\WebDriverDimension;
use function file_put_contents;
use Imagick;
use function min;

class SelfieShooter
{
    /**
     * @var WPageObject
     */
    protected $pageObject;

    public function __construct(WPageObject $pageObject)
    {
        $this->pageObject = $pageObject;
    }

    /**
     * Создаёт скриншот PageObject'а
     *
     * Все дети PageObject'а, которые реализуют интерфейс IHiddenOnShot - будут скрыты на скриншоте (замазаны цветом фона).
     *
     * Все дети PageObject'а, которые реализуют интерфейс IResetOnShot - перед скриншотом будут выставлены в дефолтное
     * состояние с помощью метода defaultStateSet(), а после скриншота - возвращены в предыдущее состояние с помощью
     * метода defaultStateUnset().
     *
     * @param string|null $filename
     * @return string
     * @throws UsageException
     * @throws \ImagickException
     */
    public function takeScreenshot(string $filename = '', $waitClosure = null) : string
    {
        WLogger::logDebug($this->pageObject . ' -> снимаем скриншот');

        $getVolatile = function (IPageObject $pageObject, array &$result) use (&$getVolatile)
        {
            WLogger::logDebug('Получаем список элементов, которые нужно скрыть/сбросить на скриншоте');

            foreach ($pageObject->getChildren() as $child)
            {
                if ($child instanceof IHiddenOnShot)
                {
                    $result['shouldBeHidden'][] = $child;
                }
                elseif ($child instanceof IResetOnShot)
                {
                    $result['shouldBeReset'][] = $child;
                }
                else
                {
                    $getVolatile($child, $result);
                }
            }
        };

        $volatileChildren = ['shouldBeReset' => [], 'shouldBeHidden' => []];
        $getVolatile($this->pageObject, $volatileChildren);

        foreach ($volatileChildren['shouldBeReset'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild . ' -> задаём дефолтное состояние');

            $volatileChild->defaultStateSet();
        }

        $rawScreenshot = $this->pageObject->returnSeleniumElement()->get()->screenshot('', $waitClosure);

        $primaryColor = $this->pageObject->returnSeleniumElement()->get()->primaryColor(null, $rawScreenshot);

        $thisRect = $this->pageObject->returnSeleniumElement()->get()->boundingClientRect();

        $screenshot = new \Imagick();
        $screenshot->readImageBlob($rawScreenshot);

        $getChildRect = function (WPageObject $child) use ($thisRect) : Rect
        {
            $childRect = $child->returnSeleniumElement()->get()->boundingClientRect();
            $childRect->x = $childRect->getX() - $thisRect->getX();
            $childRect->y = $childRect->getY() - $thisRect->getY();

            return $childRect;
        };

        foreach ($volatileChildren['shouldBeHidden'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild . ' -> скрываем на скриншоте');

            $childRect = $getChildRect($volatileChild);

            $draw = new \ImagickDraw();
            $strokeColor = new \ImagickPixel($primaryColor);
            $fillColor = new \ImagickPixel($primaryColor);

            $draw->setStrokeColor($strokeColor);
            $draw->setFillColor($fillColor);

            $draw->rectangle($childRect->getX(), $childRect->getY(), $childRect->getRight(), $childRect->getBottom());

            $screenshot->drawImage($draw);
        }

        foreach ($volatileChildren['shouldBeReset'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild . ' -> снимаем дефолтное состояние');

            $volatileChild->defaultStateUnset();
        }

        $elementScreenshot = $screenshot->getImageBlob();

        if (!empty($filename))
        {
            file_put_contents($filename, $elementScreenshot);
        }

        return $elementScreenshot;
    }

    /**
     * Сравнивает две картинки с помощью вызова метода ImageMagick compareImages (METRIC_MEANSQUAREERROR).
     *
     * Если отклонение одной картинки от другой равняется 0 или меньше параметра фреймворка "maxDeviation"
     * то будет возвращён объект Same, иначе - объект Diff, который содержит в себе diff двух картинок.
     *
     * @param string $referenceImage
     * @param string $newImage
     * @return IComparisonResult
     * @throws UsageException
     * @throws \ImagickException
     */
    public function compareImages(string $referenceImage, string $newImage) : IComparisonResult
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

        $maxDeviation = (int) TestProperties::getValue('maxDeviation');

        if ($deviation <= $maxDeviation)
        {
            WLogger::logDebug("Картинки - одинаковые ($deviation <= $maxDeviation)");

            return new Same($deviation);
        }

        WLogger::logDebug("Картинки - разные ($deviation > $maxDeviation)");

        return new Diff($deviation, $diff);
    }

    /**
     * Подгоняет картинку под заданное разрешение.
     *
     * Будет создан холст заданного разрешения, залитый чёрным цветом. Картинка будет размещена в центре холста.
     * Если картинка больше чем холст, то она будет отмасштабирована под его размер.
     *
     * @param string $imageBlob
     * @param WebDriverDimension $dimensions
     * @return string
     * @throws \ImagickException
     */
    public function fitIntoDimensions(string $imageBlob, WebDriverDimension $dimensions) : string
    {
        WLogger::logDebug('Подгоняем картинку под разрешение, если она в него не вмещается');

        $imagick = new \Imagick();
        $imagick->readImageBlob($imageBlob);

        $imageGeometry = $imagick->getImageGeometry();

        if ($imageGeometry['width'] > $dimensions->getWidth() || $imageGeometry['height'] > $dimensions->getHeight())
        {
            $imagick->scaleImage($dimensions->getWidth(), $dimensions->getHeight(), true);
        }

        $canvas = new \Imagick();
        $canvas->newImage($dimensions->getWidth(), $dimensions->getHeight(), 'black', 'PNG32');

        $imageGeometry = $imagick->getImageGeometry();

        $offsetX = (int)($dimensions->getWidth()  / 2) - (int)($imageGeometry['width']  / 2);
        $offsetY = (int)($dimensions->getHeight() / 2) - (int)($imageGeometry['height'] / 2);

        $canvas->compositeImage($imagick, imagick::COMPOSITE_OVER, $offsetX, $offsetY);

        return $canvas->getImageBlob();
    }
}
