<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetElementViewportRect extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем размер viewport'а";
    }

    /**
     * Возвращает размер viewport'а в котором находится элемент
     */
    public function __construct() {}

    public function acceptWBlock($block) : Rect
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Rect
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

    protected function apply(WPageObject $pageObject) : Rect
    {
        $rect = Rect::fromDOMRect($pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_GET_ELEMENT_VIEWPORT_SIZE));

        WLogger::logDebug('Viewport для элемента имеет размер: ' . $rect);

        return $rect;
    }

    protected const SCRIPT_GET_ELEMENT_VIEWPORT_SIZE = <<<EOF
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
}
