<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollBy;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Ds\Sequence;
use Imagick;

class GetScreenshot extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем скриншот";
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
     * Возвращает скриншот элемента
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
        WLogger::logDebug('Получаем скриншот элемента');

        if (!is_callable($this->waitClosure))
        {
            $waitClosure = function(){};
        }
        else
        {
            $waitClosure = $this->waitClosure;
        }

        $shotToCanvas = function (Imagick $imagick, Rect $viewportRect) use ($pageObject)
        {
            $imagick->readImageBlob($pageObject->returnSeleniumServer()->takeScreenshot());
            $imagick->cropImage($viewportRect->getWidth(), $viewportRect->getHeight(), $viewportRect->getX(), $viewportRect->getY());
            $imagick->setImagePage($imagick->getImageWidth(), $imagick->getImageHeight(), 0, 0);
            $imagick->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);
        };

        $getColumnShot = function (Rect $elementRect, Rect $viewportRect) use ($pageObject, $shotToCanvas, $waitClosure)
        {
            WLogger::logDebug('Делаем скриншот колонки');

            $column = new Imagick();

            $startingY = $elementRect->getY();

            /**
             * Скриншот элемента (допустим 30x100), выходящего за рамки viewport (30x30) по вертикали делается в три этапа:
             *
             * 1. Делаем скриншот видимой части viewport (кусочек 30x30)
             * 2. Прокручиваем viewport на его высоту и делаем скриншот, пока viewport можно прокрутить целое количество раз (кусочек 30x60)
             * 3. Делаем скриншот последней части элемента. Для этого нужно вычислить её высоту и прокрутить viewport на неё (кусочек 30x10).
             *
             * В конце метода мы сшиваем скриншоты полученные на этих этапах (30x30 + 30x60 + 30x10 = 30x100)
             */

            WLogger::logDebug('Делаем скриншот видимой части колонки');

            $shotToCanvas($column, $viewportRect);



            $timesY = (int) floor($elementRect->getHeight() / $viewportRect->getHeight());

            WLogger::logDebug("Для создания полного скриншота колонки, viewport будет прокручен по вертикали " . max($timesY - 1, 0) . " раз");

            for ($i = 1; $i < $timesY; $i++) //С 1 т.к. первый кусок элемента мы уже сфоткали
            {
                WLogger::logDebug('Прокручиваем viewport на его высоту и делаем скриншот');

                $pageObject->accept(new MouseScrollBy(0, $viewportRect->getHeight()));

                $waitClosure();

                $shotToCanvas($column, $viewportRect);
            }



            $danglingHeight = $elementRect->getHeight() - ($viewportRect->getHeight() * max($timesY, 1));

            if ($danglingHeight > 0)
            {
                WLogger::logDebug('Делаем скриншот последнего вертикального кусочка колонки');

                $pageObject->accept(new MouseScrollBy(0, $danglingHeight));

                $waitClosure();

                $danglingRect = Rect::fromOtherRect($viewportRect,
                                                    [
                                                        'height' => $danglingHeight,
                                                        'y' => $viewportRect->getY() + $viewportRect->getHeight() - $danglingHeight
                                                    ]);

                $shotToCanvas($column, $danglingRect);
            }



            $column->resetIterator();

            WLogger::logDebug('Сшиваем скриншоты колонки по вертикали');

            $wholeColumn = $column->appendImages(true);
            $wholeColumn->setImagePage($wholeColumn->getImageWidth(), $wholeColumn->getImageHeight(), 0, 0);
            $wholeColumn->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);

            WLogger::logDebug('Обрезаем финальный скриншот колонки до размеров элемента');

            $wholeColumn->cropImage(
                min($viewportRect->getWidth(), $elementRect->getWidth()), $elementRect->getHeight(), $elementRect->getX() -
                                                                                                     $viewportRect->getX(), $elementRect->getY() -
                                                                                                                            $viewportRect->getY()
            );



            $currentY = $pageObject->accept(new GetBoundingClientRect())->getY();
            $pageObject->accept(new MouseScrollBy(0, $currentY - $startingY)); // Прокручиваем наверх

            $waitClosure();

            return $wholeColumn;
        };

        $getColumnsShot = function (Rect $elementRect, Rect $viewportRect) use ($pageObject, $getColumnShot, $waitClosure)
        {
            WLogger::logDebug('Делаем скриншоты колонок');

            $columns = new Imagick();

            $startingX = $elementRect->getX();

            WLogger::logDebug('Делаем скриншот видимой колонки');

            $columns->addImage($getColumnShot($elementRect, $viewportRect));



            $timesX = (int) floor($elementRect->getWidth() / $viewportRect->getWidth());

            WLogger::logDebug("Для создания полного скриншота элемента, viewport будет прокручен по горизонтали " . max($timesX - 1, 0) . " раз");

            for ($j = 1; $j < $timesX; $j++)
            {
                WLogger::logDebug('Прокручиваем viewport на его ширину и делаем скриншот вертикальной колонки элемента');

                $pageObject->accept(new MouseScrollBy($viewportRect->getWidth(), 0));

                $waitClosure();

                $columns->addImage($getColumnShot($elementRect, $viewportRect));
            }



            $danglingWidth = $elementRect->getWidth() - ($viewportRect->getWidth() * max($timesX, 1));

            if ($danglingWidth > 0)
            {
                WLogger::logDebug('Делаем скриншот последнего горизонтального кусочка элемента');

                $pageObject->accept(new MouseScrollBy($danglingWidth, 0));

                $waitClosure();

                $column = $getColumnShot($elementRect, $viewportRect);
                $column->cropImage(
                    $danglingWidth, $column->getImageHeight(), $column->getImageWidth() - $danglingWidth, 0
                );

                $columns->addImage($column);
            }


            $columns->resetIterator();

            WLogger::logDebug('Сшиваем скриншоты колонок по горизонтали');

            $wholeElement = $columns->appendImages(false);
            $wholeElement->setImagePage($wholeElement->getImageWidth(), $wholeElement->getImageHeight(), 0, 0);
            $wholeElement->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);


            $currentX = $pageObject->accept(new GetBoundingClientRect())->getX();
            $pageObject->accept(new MouseScrollBy(0, $currentX - $startingX)); // Прокручиваем налево

            $waitClosure();

            return $wholeElement;
        };



        $pageObject->accept(new MouseScrollTo(0));

        $waitClosure();

        $elementRect = $pageObject->accept(new GetBoundingClientRect());

        if ($this->inSticky($pageObject))
        {
            $viewportRect = $pageObject->accept(new GetElementViewportRect());
        }
        else
        {
            $viewportRect = $pageObject->accept(new GetElementClearViewportRect());
        }

        if ($viewportRect->y < 0)
        {
            $viewportRect->y = 0; //Дурацкий Хром со своей панелькой "controlled by automated software"
        }

        $wholeElement = $getColumnsShot($elementRect, $viewportRect);

        $elementScreenshot = $wholeElement->getImageBlob();

        if (!empty($this->filename))
        {
            file_put_contents($this->filename, $elementScreenshot);
        }

        return $elementScreenshot;
    }

    protected function inSticky(WPageObject $pageObject) : bool
    {
        WLogger::logDebug('Элемент находится внутри плавающего блока?');

        $result = $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_ELEMENT_IN_STICKY);

        WLogger::logDebug(json_encode($result));

        return $result;
    }

    protected const SCRIPT_ELEMENT_IN_STICKY = <<<EOF
function inSticky(element) {
    let elements = getParents(element);

    for (const element of elements) {
        if (isSticky(element)) {
            return true;
        }
    }

    return false;
}

function getParents(element) {
    let result = [];

    while (element && element !== document) {
        result.unshift(element);
        element = element.parentNode;
    }

    return result;
}

function isSticky(element) {
    let cs = getComputedStyle(element);

    return cs['position'] === 'sticky';
}

return inSticky(arguments[0]);
EOF;
}
