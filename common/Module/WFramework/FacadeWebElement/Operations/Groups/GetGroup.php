<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 11:19
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Helpers\Color;
use Common\Module\WFramework\Helpers\Rect;
use Common\Module\WFramework\Logger\WLogger;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverPoint;
use Facebook\WebDriver\WebDriverSelect;
use Imagick;
use ImagickPixel;
use function is_callable;
use function is_numeric;
use function krsort;
use function reset;


/**
 * Категория методов FacadeWebElement, которая содержит набор методов для получения атрибутов и свойств данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class GetGroup extends OperationsGroup
{
    /**
     * Возвращает значение CSS-свойства данного элемента.
     *
     * @param string $property - CSS-свойство
     * @return string - значение свойства
     */
    public function cssValue(string $property) : string
    {
        WLogger::logDebug('Получаем значение CSS-свойства: ' . $property);

        $result =  $this->getProxyWebElement()->getCSSValue($property);

        WLogger::logDebug('CSS-свойство имеет значение: ' . $result);

        return $result;
    }

    public function cssValues(string... $properties) : array
    {
        WLogger::logDebug('Получаем значение CSS-свойств: ' . implode(', ', $properties));

        $result = [];

        foreach ($properties as $property)
        {
            $result[] = $this->cssValue($property);
        }

        WLogger::logDebug('CSS-свойства имеют значения: ' . implode(', ', $result));

        return $result;
    }

    /**
     * Возвращает видимый текст элемента, отфильтрованный по регулярке.
     *
     * @param string $regex - регулярка для фильтрации текста, если имя группы не указано, то результат регулярки должен быть в группе 1
     * @param string $groupName - опциональное имя группы
     * @return string - отфильтрованный текст
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function filteredText(string $regex, string $groupName = '') : string
    {
        WLogger::logDebug('Получаем текст, отфильтрованный по регулярке: ' . $regex);

        $text = $this->text();

        preg_match($regex, $text, $matches);

        if (empty($matches))
        {
            WLogger::logWarning("Не найдено ни одного совпадения в тексте '$text' по заданной регулярке '$regex'!");
            return '';
        }

        if (!empty($groupName) && !isset($matches[$groupName]))
        {
            throw new UsageException("В результатах заданной регулярки '$regex' нет группы '$groupName'");
        }

        $index = empty($groupName) ? 1 : $groupName;

        WLogger::logDebug('Получили отфильтрованный текст: ' . $matches[$index]);

        return $matches[$index];
    }

    public function bgColor() : Color
    {
        WLogger::logDebug('Получаем цвет фона элемента');

        $bgColor = $this->getProxyWebElement()->getCSSValue('background-color');

        $result = Color::fromString($bgColor);

        WLogger::logDebug('Получили цвет фона: ' . $result);

        return $result;
    }

    public function primaryColor(?Color $ignoredColor = null, ?string $screenshot = null) : Color
    {
        WLogger::logDebug('Получаем основной цвет элемента');

        if ($screenshot === null)
        {
            $screenshot = $this->screenshot();
        }

        $stat = $this->getHistogram($screenshot, $ignoredColor);

        krsort($stat);

        $primaryColor = reset($stat);

        WLogger::logDebug('Получили основной цвет: ' . $primaryColor);

        return $primaryColor;
    }

    public function secondaryColor(?Color $ignoredColor = null, ?string $screenshot = null) : Color
    {
        WLogger::logDebug('Получаем второстепенный цвет элемента');

        if ($screenshot === null)
        {
            $screenshot = $this->screenshot();
        }

        $stat = $this->getHistogram($screenshot, $ignoredColor);

        krsort($stat);

        $keys = array_keys($stat);
        $key = $keys[1] ?? $keys[0];

        $secondaryColor = $stat[$key];

        WLogger::logDebug('Получили второстепенный цвет: ' . $secondaryColor);

        return $secondaryColor;
    }

    protected function getHistogram(string $imageBlob, ?Color $ignoredColor = null) : array
    {
        $imagick = new Imagick();
        $imagick->readImageBlob($imageBlob);
        $histogram = $imagick->getImageHistogram();

        $stat = [];

        /** @var ImagickPixel $histogramElement */
        foreach ($histogram as $histogramElement)
        {
            $color = Color::fromImagickColor($histogramElement->getColor());

            if ($ignoredColor !== null && $color->equals($ignoredColor))
            {
                continue;
            }

            $stat[$histogramElement->getColorCount()] = $color;
        }

        return $stat;
    }

    public function borderColor() : Color
    {
        WLogger::logDebug('Получаем цвет обводки элемента');

        $borderColor = $this->getProxyWebElement()->getCSSValue('border-top-color');

        $result = Color::fromString($borderColor);

        WLogger::logDebug('Получили цвет обводки: ' . $result);

        return $result;
    }

    /**
     * Возвращает массив computed style
     *
     * @param string|null $pseudoElement
     * @return array
     */
    public function computedStyle(string $pseudoElement = null) : array
    {
        WLogger::logDebug('Получаем массив стилей элемента');

        $element = $this->getProxyWebElement();

        $computedStyle = $element->executeScriptOnThis(static::SCRIPT_GET_COMPUTED_STYLE, [$pseudoElement]);

        $result = [];

        foreach ($computedStyle as $entry)
        {
            $key = $entry[0];
            $value = $entry[1];

            if (is_numeric($key))
            {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    public function size() : WebDriverDimension
    {
        WLogger::logDebug('Получаем размер элемента');

        $result = $this->getProxyWebElement()->getSize();

        WLogger::logDebug('Элемент имеет размер: ' . $result->getWidth() . 'x' . $result->getHeight());

        return $result;
    }

    public function tag() : string
    {
        WLogger::logDebug('Получаем тэг элемента');

        $result = $this->getProxyWebElement()->getTagName();

        WLogger::logDebug('Тэг элемента: ' . $result);

        return $result;
    }

    public function location() : WebDriverPoint
    {
        WLogger::logDebug('Получаем координаты элемента (левый верхний угол)');

        $result = $this->getProxyWebElement()->getLocation();

        WLogger::logDebug(sprintf("Координаты элемента: x:%d , y:%d", $result->getX(), $result->getY()));

        return $result;
    }

    public function clientSize() : WebDriverDimension
    {
        WLogger::logDebug('Получаем внутренний размер элемента без границ и полос прокруток');

        $size = $this->getProxyWebElement()->executeScriptOnThis('return {"width": arguments[0].clientWidth, "height": arguments[0].clientHeight};');

        WLogger::logDebug('Внутренний размер элемента: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }

    public function scrollSize() : WebDriverDimension
    {
        WLogger::logDebug('Получаем реальный размер элемента');

        $size = $this->getProxyWebElement()->executeScriptOnThis('return {"width": arguments[0].scrollWidth, "height": arguments[0].scrollHeight};');

        WLogger::logDebug('Реальный размер элемента: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }

    public function scrollSizeToFitCenterOfViewport(FacadeWebElement $viewport) : WebDriverDimension
    {
        WLogger::logDebug('Получаем насколько нужно прокрутить элемент чтобы выровнять его по центру viewport\'а');

        $elementScrollSize = $this->scrollSize();
        $viewportScrollSize = $viewport->get()->clientSize();

        $inViewportHorizontalOffset = ($viewportScrollSize->getWidth() - $elementScrollSize->getWidth()) / 2;
        $inViewportVerticalOffset = ($viewportScrollSize->getHeight() - $elementScrollSize->getHeight()) / 2;

        if ($inViewportHorizontalOffset < 0 || $inViewportVerticalOffset < 0)
        {
            throw new UsageException('Элемент не вмещается в заданный viewport');
        }

        $elementLocation = $this->location();
        $viewportLocation = $viewport->get()->location();

        $incX = $elementLocation->getX() < $viewportLocation->getX();
        $incY = $elementLocation->getY() < $viewportLocation->getY();

        if ($incX)
        {
            $outViewportHorizontalOffset = $viewportLocation->getX() - $elementLocation->getX();
        }
        else
        {
            $outViewportHorizontalOffset = ($elementLocation->getX() + $elementScrollSize->getWidth()) - ($viewportLocation->getX() + $viewportScrollSize->getWidth());
        }

        if ($incY)
        {
            $outViewportVerticalOffset = $viewportLocation->getY() - $elementLocation->getY();
        }
        else
        {
            $outViewportVerticalOffset = ($elementLocation->getY() + $elementScrollSize->getHeight()) - ($viewportLocation->getY() + $viewportScrollSize->getHeight());
        }

        $horizontalOffset = $outViewportHorizontalOffset + $inViewportHorizontalOffset;
        $verticalOffset = $outViewportVerticalOffset + $inViewportVerticalOffset;

        if ($incX)
        {
            $horizontalOffset = -$horizontalOffset;
        }

        if ($incY)
        {
            $verticalOffset = -$verticalOffset;
        }

        return new WebDriverDimension($horizontalOffset, $verticalOffset);
    }

    public function viewportSize() : WebDriverDimension
    {
        WLogger::logDebug('Получаем размер viewport\'а');

        $size = $this->getProxyWebElement()->executeScript('return {"width": window.innerWidth, "height": window.innerHeight};');

        WLogger::logDebug('Viewport имеет размер: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }

    public function elementViewportRect() : Rect
    {
        WLogger::logDebug('Получаем размер viewport\'а для элемента');

        $rect = Rect::fromDOMRect($this->getProxyWebElement()->executeScriptOnThis(static::SCRIPT_GET_ELEMENT_VIEWPORT_SIZE));

        WLogger::logDebug('Viewport для элемента имеет размер: ' . $rect);

        return $rect;
    }

    public function elementClearViewportRect() : Rect
    {
        WLogger::logDebug('Получаем размер viewport\'а для элемента с учётом плавающих панелек');

        $rect = Rect::fromDOMRect($this->getProxyWebElement()->executeScriptOnThis(static::SCRIPT_GET_ELEMENT_CLEAR_VIEWPORT_SIZE));

        WLogger::logDebug('Viewport для элемента имеет размер: ' . $rect);

        return $rect;
    }

    public function boundingClientRect() : Rect
    {
        WLogger::logDebug('Получаем boundingClientRect элемента');

        $rect = Rect::fromDOMRect($this->getProxyWebElement()->executeScriptOnThis('return arguments[0].getBoundingClientRect();'));

        WLogger::logDebug('Получили boundingClientRect элемента: ' . $rect);

        return $rect;
    }

    public function inSticky() : bool
    {
        WLogger::logDebug('Элемент находится внутри плавающего блока?');

        $result = $this->getProxyWebElement()->executeScriptOnThis(static::SCRIPT_ELEMENT_IN_STICKY);

        WLogger::logDebug(json_encode($result));

        return $result;
    }

    public function screenshot(string $filename = '', $waitClosure = null) : string
    {
        WLogger::logDebug('Получаем скриншот элемента');

        if (!is_callable($waitClosure))
        {
            $waitClosure = function(){};
        }

        $shotToCanvas = function (Imagick $imagick, Rect $viewportRect)
        {
            $imagick->readImageBlob($this->getProxyWebElement()->getWebDriver()->takeScreenshot());
            $imagick->cropImage($viewportRect->getWidth(), $viewportRect->getHeight(), $viewportRect->getX(), $viewportRect->getY());
            $imagick->setImagePage($imagick->getImageWidth(), $imagick->getImageHeight(), 0, 0);
            $imagick->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);
        };

        $getColumnShot = function (Rect $elementRect, Rect $viewportRect) use ($shotToCanvas, $waitClosure)
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

                $this->facadeWebElement->mouse()->scrollBy(0, $viewportRect->getHeight());

                $waitClosure();

                $shotToCanvas($column, $viewportRect);
            }



            $danglingHeight = $elementRect->getHeight() - ($viewportRect->getHeight() * max($timesY, 1));

            if ($danglingHeight > 0)
            {
                WLogger::logDebug('Делаем скриншот последнего вертикального кусочка колонки');

                $this->facadeWebElement->mouse()->scrollBy(0, $danglingHeight);

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



            $currentY = $this->facadeWebElement->get()->boundingClientRect()->getY();
            $this->facadeWebElement->mouse()->scrollBy(0, $currentY - $startingY); // Прокручиваем наверх

            $waitClosure();

            return $wholeColumn;
        };

        $getColumnsShot = function (Rect $elementRect, Rect $viewportRect) use ($getColumnShot, $waitClosure)
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

                $this->facadeWebElement->mouse()->scrollBy($viewportRect->getWidth(), 0);

                $waitClosure();

                $columns->addImage($getColumnShot($elementRect, $viewportRect));
            }



            $danglingWidth = $elementRect->getWidth() - ($viewportRect->getWidth() * max($timesX, 1));

            if ($danglingWidth > 0)
            {
                WLogger::logDebug('Делаем скриншот последнего горизонтального кусочка элемента');

                $this->facadeWebElement->mouse()->scrollBy($danglingWidth, 0);

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


            $currentX = $this->facadeWebElement->get()->boundingClientRect()->getX();
            $this->facadeWebElement->mouse()->scrollBy(0, $currentX - $startingX); // Прокручиваем налево

            $waitClosure();

            return $wholeElement;
        };



        $this->facadeWebElement->mouse()->scrollTo(0);

        $waitClosure();

        $elementRect = $this->boundingClientRect();

        if ($this->inSticky())
        {
            $viewportRect = $this->elementViewportRect();
        }
        else
        {
            $viewportRect = $this->elementClearViewportRect();
        }

        if ($viewportRect->y < 0)
        {
            $viewportRect->y = 0; //Дурацкий Хром со своей панелькой "controlled by automated software"
        }

        $wholeElement = $getColumnsShot($elementRect, $viewportRect);

        $elementScreenshot = $wholeElement->getImageBlob();

        if (!empty($filename))
        {
            file_put_contents($filename, $elementScreenshot);
        }

        return $elementScreenshot;
    }

    const SCRIPT_GET_COMPUTED_STYLE = <<<EOF

var result = [];

var obj = window.getComputedStyle(arguments[0], arguments[1]);

for(var key in obj) {
    var value = obj[key];
    result.push([key, value]);
}

return result;
EOF;

    const SCRIPT_GET_ELEMENT_VIEWPORT_SIZE = <<<EOF
function getScrollParent(element, includeHidden) {
    var style = getComputedStyle(element);
    var excludeStaticParent = style.position === "absolute";
    var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;
    
    if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return element;

    if (style.position === "fixed") return document.documentElement;
    for (var parent = element; (parent = parent.parentElement);) {
        style = getComputedStyle(parent);
        if (excludeStaticParent && style.position === "static") {
            continue;
        }
        if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
    }

    return document.documentElement;
}

function getElementViewportSize(element) {
    let scrollParent = getScrollParent(element, false);

    return scrollParent.getBoundingClientRect();
}

return getElementViewportSize(arguments[0]);
EOF;

    const SCRIPT_GET_ELEMENT_CLEAR_VIEWPORT_SIZE = <<<EOF
return getElementViewportSize(arguments[0]);

function getElementViewportSize(element) {
    let scrollParent = getScrollParent(element, false);
    
    return getClearViewport(scrollParent);
}

function getScrollParent(element, includeHidden) {
    var style = getComputedStyle(element);
    var excludeStaticParent = style.position === "absolute";
    var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

    if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return element;

    if (style.position === "fixed") return document.documentElement;
    for (var parent = element; (parent = parent.parentElement);) {
        style = getComputedStyle(parent);
        if (excludeStaticParent && style.position === "static") {
            continue;
        }
        if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
    }

    return document.documentElement;
}

function getViewportStickies(viewport) {
    let result = [];

    let elements = viewport.getElementsByTagName("*");

    for (const element of elements) {
        let cs = getComputedStyle(element);
        let position = cs['position'];

        if (position !== 'sticky') {
            continue;
        }

        result.push(getStickyPosition(element, cs));
    }

    return result;
}

function getStickyPosition(sticky, computedStyle) {
    let positions = ['top', 'bottom', 'left', 'right'];

    for (const pos of positions) {
        let value = computedStyle[pos] ?? 'auto';

        if (isNaN(parseInt(value, 10))) {
            continue;
        }

        let pixels = toPixels(sticky, value, pos);

        return { element: sticky, position: pos, value: pixels };
    }

    return { element: sticky, position: "top", value: 0 };
}

function getCutValues(stickies) {
    var cutValues = { top: [0], bottom: [0], left: [0], right: [0] };

    for (sticky of stickies) {
        let { element, position, value } = sticky;

        let rect = element.getBoundingClientRect();

        var cutValue = 0;

        if (["top", "bottom"].indexOf(position) > -1) {
            cutValue = rect.height + value;
        } else {
            cutValue = rect.width + value;
        }

        cutValues[position].push(cutValue);
    }

    return {
        top: Math.max(...cutValues.top),
        bottom: Math.max(...cutValues.bottom),
        left: Math.max(...cutValues.left),
        right: Math.max(...cutValues.right)
    };
}

function getClearViewport(viewport) {
    let stickies = getViewportStickies(viewport);

    var viewportRect = viewport.getBoundingClientRect();

    if (!Array.isArray(stickies) || !stickies.length) {
        return viewportRect;
    }

    let cutValues = getCutValues(stickies);

    viewportRect = new DOMRect(viewportRect.x, viewportRect.y + cutValues.top, viewportRect.width, viewportRect.height - cutValues.top - cutValues.bottom);
    viewportRect = new DOMRect(viewportRect.x + cutValues.left, viewportRect.y, viewportRect.width - cutValues.left - cutValues.right, viewportRect.height);

    return viewportRect;
}


//https://github.com/heygrady/Units

function toPixels(elem, value, prop) {
    // create a test element
    var testElem = document.createElement('test'),
        docElement = document.documentElement,
        defaultView = document.defaultView,
        getComputedStyle = defaultView && defaultView.getComputedStyle,
        runit = /^(-?[\d+\.\-]+)([a-z]+|%)$/i,
        convert = {},
        conversions = [1 / 25.4, 1 / 2.54, 1 / 72, 1 / 6],
        units = ['mm', 'cm', 'pt', 'pc', 'in', 'mozmm'],
        i = 6; // units.length

    // add the test element to the dom
    docElement.appendChild(testElem);

    // pre-calculate absolute unit conversions
    while (i--) {
        convert[units[i] + "toPx"] = conversions[i] ? conversions[i] * convert.inToPx : toPx(testElem, '1' + units[i]);
    }

    // remove the test element from the DOM and delete it
    docElement.removeChild(testElem);
    testElem = undefined;

    // convert a value to pixels
    function toPx(elem, value, prop, force) {
        // use width as the default property, or specify your own
        prop = prop || 'width';

        var style,
            inlineValue,
            ret,
            unit = (value.match(runit) || [])[2],
            conversion = unit === 'px' ? 1 : convert[unit + 'toPx'],
            rem = /r?em/i;

        if (conversion || rem.test(unit) && !force) {
            // calculate known conversions immediately
            // find the correct element for absolute units or rem or fontSize + em or em
            elem = conversion ? elem : unit === 'rem' ? docElement : prop === 'fontSize' ? elem.parentNode || elem : elem;

            // use the pre-calculated conversion or fontSize of the element for rem and em
            conversion = conversion || parseFloat(curCSS(elem, 'fontSize'));

            // multiply the value by the conversion
            ret = parseFloat(value) * conversion;
        } else {
            // begin "the awesome hack by Dean Edwards"
            // @see http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

            // remember the current style
            style = elem.style;
            inlineValue = style[prop];

            // set the style on the target element
            try {
                style[prop] = value;
            } catch (e) {
                // IE 8 and below throw an exception when setting unsupported units
                return 0;
            }

            // read the computed value
            // if style is nothing we probably set an unsupported unit
            ret = !style[prop] ? 0 : parseFloat(curCSS(elem, prop));

            // reset the style back to what it was or blank it out
            style[prop] = inlineValue !== undefined ? inlineValue : null;
        }

        // return a number
        return ret;
    }

    // return the computed value of a CSS property
    function curCSS(elem, prop) {
        var value,
            pixel,
            unit,
            rvpos = /^top|bottom/,
            outerProp = ["paddingTop", "paddingBottom", "borderTop", "borderBottom"],
            innerHeight,
            parent,
            i = 4; // outerProp.length

        if (getComputedStyle) {
            // FireFox, Chrome/Safari, Opera and IE9+
            value = getComputedStyle(elem)[prop];
        } else if (pixel = elem.style['pixel' + prop.charAt(0).toUpperCase() + prop.slice(1)]) {
            // IE and Opera support pixel shortcuts for top, bottom, left, right, height, width
            // WebKit supports pixel shortcuts only when an absolute unit is used
            value = pixel + 'px';
        } else if (prop === 'fontSize') {
            // correct IE issues with font-size
            // @see http://bugs.jquery.com/ticket/760
            value = toPx(elem, '1em', 'left', 1) + 'px';
        } else {
            // IE 8 and below return the specified style
            value = elem.currentStyle[prop];
        }

        // check the unit
        unit = (value.match(runit) || [])[2];
        if ((value === 'auto' || (unit && unit !== 'px')) && getComputedStyle) {
            // WebKit and Opera will return auto in some cases
            // Firefox will pass back an unaltered value when it can't be set, like top on a static element
            value = 0;
        } else if (unit && unit !== 'px' && !getComputedStyle) {
            // IE 8 and below won't convert units for us
            // try to convert using a prop that will return pixels
            // this will be accurate for everything (except font-size and some percentages)
            value = toPx(elem, value) + 'px';
        }
        return value;
    }

    return toPx(elem, value, prop);
}
EOF;

    const SCRIPT_ELEMENT_IN_STICKY = <<<EOF
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
