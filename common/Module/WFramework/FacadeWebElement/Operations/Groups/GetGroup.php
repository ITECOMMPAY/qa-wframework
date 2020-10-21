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
     * Возвращает видимый текст элемента.
     *
     * @return string - видимый текст элемента.
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function text() : string
    {
        WLogger::logDebug('Получаем текст');

        $element = $this->getProxyWebElement();

        $tag = $element->getTagName();

        $result = '';

        if (strcasecmp('select', $tag) === 0)
        {
            $select = new WebDriverSelect($element);

            $result = $select
                        ->getFirstSelectedOption()
                        ->getText()
                        ;
        }
        else
        {
            $result = $element->getText();
        }

        WLogger::logDebug('Получили текст: ' . $result);

        return $result;
    }

    /**
     * Получает сырой текст элемента (включая невидимый)
     *
     * @return string
     */
    public function rawText() : string
    {
        WLogger::logDebug('Получаем сырой текст элемента (включая невидимый)');

        $element = $this->getProxyWebElement();

        $result = $element->executeScriptOnThis(static::SCRIPT_GET_TEXT);

        WLogger::logDebug('Получили сырой текст: ' . $result);

        return $result;
    }

    /**
     * Возвращает значение атрибута данного элемента.
     *
     * @param string $attribute - атрибут
     * @return null|string - значение атрибута, или null - если атрибут не найден
     */
    public function attribute(string $attribute)
    {
        WLogger::logDebug('Получаем значение атрибута: ' . $attribute);

        $result = $this
                    ->getProxyWebElement()
                    ->getAttribute($attribute)
                    ;

        WLogger::logDebug('Атрибут имеет значение: ' . json_encode($result));

        return $result;
    }

    /**
     * Возвращает значение атрибута 'value' данного элемента.
     *
     * @return null|string - значение атрибута 'value', или null - если атрибут не найден
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function value()
    {
        WLogger::logDebug('Получаем значение');

        $element = $this->getProxyWebElement();

        $tag = $element->getTagName();

        $result = '';

        if (strcasecmp('select', $tag) === 0)
        {
            $select = new WebDriverSelect($element);

            $result = $select
                        ->getFirstSelectedOption()
                        ->getAttribute('value')
                        ;
        }
        else
        {
            $result = $element->getAttribute('value');
        }

        WLogger::logDebug('Получили значение: ' . $result);

        return $result;
    }

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

        $borderColor = $this->getProxyWebElement()->getCSSValue('border-color');

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

    public function boundingClientRect() : Rect
    {
        WLogger::logDebug('Получаем boundingClientRect элемента');

        $rect = Rect::fromDOMRect($this->getProxyWebElement()->executeScriptOnThis('return arguments[0].getBoundingClientRect();'));

        WLogger::logDebug('Получили boundingClientRect элемента: ' . $rect);

        return $rect;
    }

    public function screenshot(string $filename = '', $waitClosure = null) : string
    {
        WLogger::logDebug('Получаем скриншот элемента');

        $this->facadeWebElement->mouse()->scrollTo(0);

        if (!is_callable($waitClosure))
        {
            $waitClosure = function(){};
        }

        $waitClosure();

        $elementRect = $this->boundingClientRect();
        $viewportRect = $this->elementViewportRect();

        $getColumnShot = function (Rect $elementRect, Rect $viewportRect) use ($waitClosure)
        {
            $column = new Imagick();

            $startingY = $this->facadeWebElement->get()->boundingClientRect()->getY();

            $timesY = (int) floor($elementRect->getHeight() / $viewportRect->getHeight());

            $timesY = ($timesY < 1) ? 1 : $timesY;

            WLogger::logDebug("Для создания полного скриншота, viewport будет прокручен $timesY раз");

            for ($i = 0; $i < $timesY; $i++)
            {
                WLogger::logDebug('Делаем скриншот viewport и прокручиваем его на его высоту');

                $screenshot = $this->getProxyWebElement()->getWebDriver()->takeScreenshot();

                $column->readImageBlob($screenshot);
                $column->cropImage($viewportRect->getWidth(), $viewportRect->getHeight(), $viewportRect->getX(), $viewportRect->getY());
                $column->setImagePage($column->getImageWidth(), $column->getImageHeight(), 0, 0);
                $column->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);

                $this->facadeWebElement->mouse()->scrollBy(0, $viewportRect->getHeight());
                $waitClosure();
            }

            $danglingHeight = $elementRect->getHeight() - ($viewportRect->getHeight() * $timesY);

            $currentY = $this->facadeWebElement->get()->boundingClientRect()->getY();

            if ($danglingHeight > 0)
            {
                WLogger::logDebug('Делаем скриншот последнего кусочка viewport');

                $danglingFromTop = ($currentY - $startingY) % $viewportRect->getHeight() == 0;

                $screenshot = $this->getProxyWebElement()->getWebDriver()->takeScreenshot();
                $column->readImageBlob($screenshot);

                if ($danglingFromTop)
                {
                    $column->cropImage(
                        $viewportRect->getWidth(), $danglingHeight, $viewportRect->getX(), $viewportRect->getY());
                }
                else
                {
                    $column->cropImage(
                        $viewportRect->getWidth(), $danglingHeight, $viewportRect->getX(), $viewportRect->getY() +
                                                                                           ($viewportRect->getHeight() -
                                                                                            $danglingHeight)
                    );
                }
            }

            $column->resetIterator();

            WLogger::logDebug('Сшиваем скриншоты по вертикали');

            $wholeColumn = $column->appendImages(true);
            $wholeColumn->setImagePage($wholeColumn->getImageWidth(), $wholeColumn->getImageHeight(), 0, 0);
            $wholeColumn->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);

            WLogger::logDebug('Обрезаем финальный скриншот до размеров элемента');

            $wholeColumn->cropImage(
                min($viewportRect->getWidth(), $elementRect->getWidth()), $elementRect->getHeight(), $elementRect->getX() -
                                                                     $viewportRect->getX(), $elementRect->getY() -
                                                                                            $viewportRect->getY()
            );

            $this->facadeWebElement->mouse()->scrollBy(0, $currentY - $startingY);
            $waitClosure();

            return $wholeColumn;
        };

        $startingX = $this->facadeWebElement->get()->boundingClientRect()->getX();

        $timesX = (int) floor($elementRect->getWidth() / $viewportRect->getWidth());

        WLogger::logDebug("Для создания полного скриншота, viewport будет прокручен по горизонтали $timesX раз");

        $timesX = ($timesX < 1) ? 1 : $timesX;

        $columns = new Imagick();

        for ($j = 0; $j < $timesX; $j++)
        {
            WLogger::logDebug('Делаем скриншот вертикальной колонки элемента и прокручиваем его viewport на его ширину');

            $columns->addImage($getColumnShot($elementRect, $viewportRect));

            $this->facadeWebElement->mouse()->scrollBy($viewportRect->getWidth(), 0);
            $waitClosure();
        }

        $danglingWidth = $elementRect->getWidth() - ($viewportRect->getWidth() * $timesX);

        $currentX = $this->facadeWebElement->get()->boundingClientRect()->getX();

        if ($danglingWidth > 0)
        {
            WLogger::logDebug('Делаем скриншот последнего горизонтального кусочка viewport');

            $column = $getColumnShot($elementRect, $viewportRect);

            $danglingFromLeft = ($currentX - $startingX) % $viewportRect->getWidth() == 0;

            if ($danglingFromLeft)
            {
                $column->cropImage(
                    $danglingWidth, $column->getImageHeight(), 0, 0
                );
            }
            else
            {
                $column->cropImage(
                    $danglingWidth, $column->getImageHeight(), $column->getImageWidth() - $danglingWidth, 0
                );
            }

            $columns->addImage($column);
        }

        $columns->resetIterator();

        WLogger::logDebug('Сшиваем скриншоты по горизонтали');

        $wholeElement = $columns->appendImages(false);
        $wholeElement->setImagePage($wholeElement->getImageWidth(), $wholeElement->getImageHeight(), 0, 0);
        $wholeElement->setImageUnits(imagick::PATHUNITS_OBJECTBOUNDINGBOX);

        $elementScreenshot = $wholeElement->getImageBlob();

        if (!empty($filename))
        {
            file_put_contents($filename, $elementScreenshot);
        }

        return $elementScreenshot;
    }

    const SCRIPT_GET_TEXT = <<<EOF
let content = arguments[0].textContent;

if (content === "")
{
    content = arguments[0].getAttribute('value') ?? '';
}

return content;
EOF;

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

    if (style.position === "fixed") return document.body;
    for (var parent = element; (parent = parent.parentElement);) {
        style = getComputedStyle(parent);
        if (excludeStaticParent && style.position === "static") {
            continue;
        }
        if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
    }

    return document.body;
}

function getElementViewportSize(element) {
    let scrollParent = getScrollParent(element, true);
    
    return scrollParent.getBoundingClientRect();
}

return getElementViewportSize(arguments[0]);
EOF;

}
