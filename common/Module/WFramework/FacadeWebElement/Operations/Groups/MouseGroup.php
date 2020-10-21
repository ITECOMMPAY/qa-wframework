<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 11:32
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use Facebook\WebDriver\Exception\UnknownServerException;
use Facebook\WebDriver\Exception\WebDriverException;
use function strpos;


/**
 * Категория методов FacadeWebElement, которая содержит набор методов для эмуляции действий мыши для данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class MouseGroup extends OperationsGroup
{
    /**
     * Осуществляет клик на данном элементе.
     *
     * Если в настройках тестового модуля опция clickViaJS стоит в True, то клик на элементе будет осуществляться
     * посредством JavaScript. Такой клик игнорирует перекрытие данного элемента другим элементом.
     *
     * Если в настройках тестового модуля опция autoClickViaJS стоит в True, то первый клик на элементе будет
     * осуществляться посредством Селениума, и, если элемент окажется перекрыт, то будет произведён второй клик,
     * посредством JavaScript.
     *
     * @return MouseGroup
     * @throws UnknownServerException - элемент перекрыт другим элементом
     * @throws WebDriverException
     */
    public function click() : MouseGroup
    {
        WLogger::logDebug('Кликаем на элементе');

        $element = $this->getProxyWebElement();

        $clickViaJs = (bool) TestProperties::getValue('clickViaJS');

        if ($clickViaJs)
        {
            return $this->clickViaJS();
        }

        try
        {
            $element->click();
        }
        catch (WebDriverException $e)
        {
            if (strpos($e->getMessage(), 'is not clickable at point') === False)
            {
                throw $e;
            }

            $otherElement = '';

            if (preg_match('/Other element would receive the click: (?\'element\'.+) \(Session info/ms', $e->getMessage(), $matches) === 1)
            {
                $otherElement = $matches['element'];
            }

            WLogger::logWarning('Не получается кликнуть на элементе - он перекрыт другим элементом: ' . $otherElement);

            $autoClickViaJS = (bool) TestProperties::getValue('autoClickViaJS', False);

            if ($autoClickViaJS)
            {
                WLogger::logDebug('autoClickViaJS == True -> пробуем кликнуть с помощью JS');

                return $this->clickViaJS();
            }
        }

        return $this;
    }

    public function clickViaJS() : MouseGroup
    {
        WLogger::logDebug('Кликаем на элементе, используя JavaScript');

        $element = $this->getProxyWebElement();

        $element->executeScriptOnThis('arguments[0].click()');
        return $this;
    }

    /**
     * Осуществляет двойной клик на данном элементе с помощью Selenium Actions
     *
     * @param int $offsetX - опциональное смещение от центра элемента по оси X
     * @param int $offsetY - опциональное смещение от центра элемента по оси Y
     * @return MouseGroup
     */
    public function doubleClick(int $offsetX = 0, int $offsetY = 0) : MouseGroup
    {
        WLogger::logDebug("Выполняем двойной клик на элементе, смещение от центра: X$offsetX, Y$offsetY");

        $element = $this->getProxyWebElement();

        $element
            ->executeActions()
            ->moveToElement($offsetX, $offsetY)
            ->doubleClick()
            ->perform()
        ;

        return $this;
    }

    /**
     * Перемещает курсор поверх данного элемента с помощью Selenium Actions
     *
     * @param int $offsetX - опциональное смещение от центра элемента по оси X
     * @param int $offsetY - опциональное смещение от центра элемента по оси Y
     * @return MouseGroup
     */
    public function moveOver(int $offsetX = 0, int $offsetY = 0) : MouseGroup
    {
        WLogger::logDebug("Двигаем курсор на элемент, смещение от центра: X$offsetX, Y$offsetY");

        $element = $this->getProxyWebElement();

        $element
            ->executeActions()
            ->moveToElement($offsetX, $offsetY)
            ->perform()
        ;

        return $this;
    }

    /**
     * Осуществляет клик левой кнопкой мыши на данном элементе с помощью Selenium Actions
     *
     * @param int $offsetX - опциональное смещение от центра элемента по оси X
     * @param int $offsetY - опциональное смещение от центра элемента по оси Y
     * @return MouseGroup
     */
    public function clickWithLeftButton(int $offsetX = 0, int $offsetY = 0) : MouseGroup
    {
        WLogger::logDebug("Кликаем левой кнопкой мыши на элементе, смещение от центра: X$offsetX, Y$offsetY");

        $element = $this->getProxyWebElement();

        $element
            ->executeActions()
            ->moveToElement($offsetX, $offsetY)
            ->click()
            ->perform()
        ;

        return $this;
    }

    /**
     * Осуществляет клик правой кнопкой мыши на данном элементе с помощью Selenium Actions
     *
     * @param int $offsetX - опциональное смещение от центра элемента по оси X
     * @param int $offsetY - опциональное смещение от центра элемента по оси Y
     * @return MouseGroup
     */
    public function clickWithRightButton(int $offsetX = 0, int $offsetY = 0) : MouseGroup
    {
        WLogger::logDebug("Кликаем правой кнопкой мыши на элементе, смещение от центра: X$offsetX, Y$offsetY");

        $element = $this->getProxyWebElement();

        $element
            ->executeActions()
            ->moveToElement($offsetX, $offsetY)
            ->contextClick()
            ->perform()
        ;

        return $this;
    }

    /**
     * Скроллит к данному элементу
     *
     * @return MouseGroup
     */
    public function scrollTo(int $topOffset = null) : MouseGroup
    {
        WLogger::logDebug('Скроллим к элементу');

        $forceScrollToOff = (bool) TestProperties::getValue('forceScrollToOff', false);

        if ($forceScrollToOff)
        {
            WLogger::logDebug('Скроллинг к элементу запрещён (forceScrollToOff: true)');

            return $this;
        }

        $element = $this->getProxyWebElement();

        $topOffset = $topOffset ?? (int) TestProperties::getValue('topBarHeight', 0);

        $element->executeScriptOnThis(static::SCRIPT_SCROLL_INTO_VIEW, [$topOffset]);

        return $this;
    }

    /**
     * Скроллит данный элемент на X, Y
     *
     * @param int $x - смещение по оси X
     * @param int $y - смещение по оси Y
     * @return MouseGroup
     */
    public function scrollBy(int $x = 0, int $y = 0) : MouseGroup
    {
        WLogger::logDebug("Скроллим элемент на $x по X, и $y по Y");

        $element = $this->getProxyWebElement();

        $element->executeScriptOnThis(static::SCRIPT_SCROLL_BY, [$x, $y]);

        return $this;
    }

    /**
     * зажимает ЛКМ на элементе и двигает курсор на X, Y; затем отпускает;
     * фактически, драг-н-дроп
     *
     * @param int $offsetX - смещение по оси X
     * @param int $offsetY - смещение по оси Y
     * @return MouseGroup
     */
    public function clickHoldAndMove(int $offsetX = 0, int $offsetY = 0) : MouseGroup
    {
        WLogger::logDebug("Зажимаем ЛКМ на элементе и двигаем по оффсетам: X$offsetX, Y$offsetY");

        $element = $this->getProxyWebElement();
        $element
            ->executeActions()
            ->dragAndDropBy($offsetX, $offsetY)
            ->perform();
        return $this;
    }

    /**
     * Кликает по координатам (относительно вьюпорта)
     *
     * @param int $x
     * @param int $y
     * @return MouseGroup
     */
    public function clickAtCoordinates(int $x, int $y) : MouseGroup
    {
        $element = $this->getProxyWebElement();

        $element->executeScriptOnThis(static::CLICK_AT_COORDINATES, [$x, $y]);

        return $this;
    }

    const SCRIPT_SCROLL_INTO_VIEW = <<<EOF
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

arguments[0].scrollIntoView(true);

if (arguments[1] === 0)
{
    return;
}

let scrollParent = getScrollParent(arguments[0], true);

if (Math.abs(scrollParent.getBoundingClientRect().y - arguments[0].getBoundingClientRect().y) >= arguments[1])
{
    return;
}

scrollParent.scroll(0, scrollParent.scrollTop - arguments[1]);
EOF;

    const SCRIPT_SCROLL_BY = <<<EOF
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

getScrollParent(arguments[0], true).scrollBy(arguments[1], arguments[2]);
EOF;

    const CLICK_AT_COORDINATES = <<<EOF
function clickAtCoordinates(x,y){
    var el = document.elementFromPoint(x,y);
    
    var ev = document.createEvent("MouseEvent");
    ev.initMouseEvent(
        "mouseover",
        true /* bubble */, true /* cancelable */,
        window, null,
        x, y, 0, 0, /* coordinates */
        false, false, false, false, /* modifier keys */
        0 /*left*/, null
    );
    el.dispatchEvent(ev);
    
    var ev = document.createEvent("MouseEvent");
    ev.initMouseEvent(
        "mousedown",
        true /* bubble */, true /* cancelable */,
        window, null,
        x, y, 0, 0, /* coordinates */
        false, false, false, false, /* modifier keys */
        0 /*left*/, null
    );
    el.dispatchEvent(ev);
    
    var ev = document.createEvent("MouseEvent");
    ev.initMouseEvent(
        "mouseup",
        true /* bubble */, true /* cancelable */,
        window, null,
        x, y, 0, 0, /* coordinates */
        false, false, false, false, /* modifier keys */
        0 /*left*/, null
    );
    el.dispatchEvent(ev);
    
    var ev = document.createEvent("MouseEvent");
    ev.initMouseEvent(
        "click",
        true /* bubble */, true /* cancelable */,
        window, null,
        x, y, 0, 0, /* coordinates */
        false, false, false, false, /* modifier keys */
        0 /*left*/, null
    );
    el.dispatchEvent(ev);
}

clickAtCoordinates(arguments[1], arguments[2]);
EOF;

}
