<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class MouseScrollTo extends AbstractOperation
{
    public function getName() : string
    {
        return "скроллим к элементу со смещением от верхней границы экрана: $this->topOffset";
    }

    /**
     * @var int|null
     */
    protected $topOffset;

    /**
     * Скроллит к данному элементу
     *
     * @param int|null $topOffset - опциональная высота верхней панельки, которая перекрывает элементы
     */
    public function __construct(int $topOffset = null)
    {
        $this->topOffset = $topOffset ?? (int) TestProperties::getValue('topBarHeight', 0);
    }

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Скроллим к элементу');

        $forceScrollToOff = (bool) TestProperties::getValue('forceScrollToOff', false);

        if ($forceScrollToOff)
        {
            WLogger::logDebug('Скроллинг к элементу запрещён (forceScrollToOff: true)');
            return;
        }

        $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_SCROLL_INTO_VIEW, [$this->topOffset]);
    }

    protected const SCRIPT_SCROLL_INTO_VIEW = <<<EOF
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

arguments[0].scrollIntoView(true);

if (arguments[1] === 0)
{
    return;
}

let scrollParent = getScrollParent(arguments[0], false);

let scrollParentY = scrollParent.getBoundingClientRect().y > 0 ? scrollParent.getBoundingClientRect().y : 0;

if (Math.abs(scrollParentY - arguments[0].getBoundingClientRect().y) >= arguments[1])
{
    return;
}

scrollParent.scroll(0, scrollParent.scrollTop - arguments[1]);
EOF;

}
