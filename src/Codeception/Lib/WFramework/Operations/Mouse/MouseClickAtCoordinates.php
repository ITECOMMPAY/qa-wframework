<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class MouseClickAtCoordinates extends AbstractOperation
{
    public function getName() : string
    {
        return "кликаем на координатах X:$this->x; Y:$this->y";
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
     * Кликает по координатам (относительно вьюпорта)
     */
    public function __construct(int $x, int $y)
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

    protected function apply(WPageObject $pageObject)
    {
        $pageObject->returnSeleniumElement()->executeScriptOnThis(static::CLICK_AT_COORDINATES, [$this->x, $this->y]);
    }

    protected const CLICK_AT_COORDINATES = <<<EOF
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
