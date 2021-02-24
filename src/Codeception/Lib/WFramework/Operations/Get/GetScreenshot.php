<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Conditions\PageLoaded;
use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHiddenOnShot;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IResetOnShot;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Sequence;

class GetScreenshot extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем скриншот (скрывая IHiddenOnShot элементы и сбрасывая IResetOnShot элементы)";
    }

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var null
     */
    protected $waitClosure;

    /**
     * Возвращает скриншот PageObject'а
     *
     * Все дети PageObject'а, которые реализуют интерфейс IHiddenOnShot - будут скрыты на скриншоте (замазаны цветом фона).
     *
     * Все дети PageObject'а, которые реализуют интерфейс IResetOnShot - перед скриншотом будут выставлены в дефолтное
     * состояние с помощью метода defaultStateSet(), а после скриншота - возвращены в предыдущее состояние с помощью
     * метода defaultStateUnset().
     *
     * @param string $filename - скриншот так же будет сохранён в этот файл
     * @param null $waitClosure - опциональный метод, который будет вызываться после каждого прокручивания экрана для ожидания
     */
    public function __construct(string $filename = '', $waitClosure = null)
    {
        $this->filename = $filename;
        $this->waitClosure = $waitClosure;
    }

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    public function apply(WPageObject $pageObject) : string
    {
        $getVolatile = function (IPageObject $pageObject, array &$result) use (&$getVolatile)
        {
            WLogger::logDebug($this, 'Получаем список элементов, которые нужно скрыть/сбросить на скриншоте');

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

        $pageObject->finally_(new PageLoaded());

        $volatileChildren = ['shouldBeReset' => [], 'shouldBeHidden' => []];
        $getVolatile($pageObject, $volatileChildren);

        /** @var IResetOnShot|IPageObject $volatileChild */
        foreach ($volatileChildren['shouldBeReset'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild, 'задаём дефолтное состояние');

            $volatileChild->defaultStateSet();
        }

        $rawScreenshot = $pageObject->accept(new GetScreenshotRaw('', $this->waitClosure));

        $primaryColor = $pageObject->accept(new GetPrimaryColor(null, $rawScreenshot));

        $thisRect = $pageObject->accept(new GetBoundingClientRect());

        $screenshot = new \Imagick();
        $screenshot->readImageBlob($rawScreenshot);

        $getChildRect = function (IPageObject $child) use ($thisRect) : Rect
        {
            $childRect = $child->accept(new GetBoundingClientRect());
            $childRect->x = $childRect->getX() - $thisRect->getX();
            $childRect->y = $childRect->getY() - $thisRect->getY();

            return $childRect;
        };

        /** @var IHiddenOnShot|IPageObject $volatileChild */
        foreach ($volatileChildren['shouldBeHidden'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild, 'скрываем на скриншоте');

            $childRect = $getChildRect($volatileChild);

            $draw = new \ImagickDraw();
            $strokeColor = new \ImagickPixel($primaryColor);
            $fillColor = new \ImagickPixel($primaryColor);

            $draw->setStrokeColor($strokeColor);
            $draw->setFillColor($fillColor);

            $draw->rectangle($childRect->getX(), $childRect->getY(), $childRect->getRight(), $childRect->getBottom());

            $screenshot->drawImage($draw);
        }

        /** @var IResetOnShot|IPageObject $volatileChild */
        foreach ($volatileChildren['shouldBeReset'] as $volatileChild)
        {
            WLogger::logDebug($volatileChild, 'снимаем дефолтное состояние');

            $volatileChild->defaultStateUnset();
        }

        $elementScreenshot = $screenshot->getImageBlob();

        if (!empty($this->filename))
        {
            file_put_contents($this->filename, $elementScreenshot);
        }

        return $elementScreenshot;
    }
}