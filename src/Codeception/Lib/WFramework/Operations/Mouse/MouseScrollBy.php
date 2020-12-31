<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class MouseScrollBy extends AbstractOperation
{
    public function getName() : string
    {
        return "скроллим на смещение ($this->x; $this->y)";
    }

    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * Скроллит данный элемент на X, Y
     *
     * @param int $x - смещение по оси X
     * @param int $y - смещение по оси Y
     */
    public function __construct(int $x = 0, int $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
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
        $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_SCROLL_BY, [$this->x, $this->y]);
    }

    protected const SCRIPT_SCROLL_BY = <<<EOF
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

getScrollParent(arguments[0], false).scrollBy(arguments[1], arguments[2]);
EOF;

}
