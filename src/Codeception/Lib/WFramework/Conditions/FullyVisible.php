<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class FullyVisible extends AbstractCondition
{
    public function getName() : string
    {
        return "отображается целиком?";
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        return (bool) $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_IN_VIEW));
    }

    protected const SCRIPT_IN_VIEW = <<<EOF
function isDisplayedInViewport (elem) {
    var box = elem.getBoundingClientRect();
    
    var pixelDev = Math.min(box.width, box.height) >= 6 ? 4 : 1;

    var hdev = ~~(box.width / 25);
    hdev = hdev <= pixelDev ? pixelDev : hdev;

    var vdev = ~~(box.height / 25);
    vdev = vdev <= pixelDev ? pixelDev : vdev;

    var lx = box.left + hdev;
    var ly = box.top + box.height / 2;

    var rx = box.left + box.width - hdev;
    var ry = box.top + box.height / 2;

    var tx = box.left + box.width / 2;
    var ty = box.top + vdev;

    var bx = box.left + box.width / 2;
    var by = box.top + box.height - vdev;

    return isVisibleAtPoint(elem, lx, ly) && isVisibleAtPoint(elem, rx, ry) && isVisibleAtPoint(elem, tx, ty) && isVisibleAtPoint(elem, bx, by);
}

function isVisibleAtPoint(elem, x, y)
{
    var e = document.elementFromPoint(x, y);

    for (; e; e = e.parentElement) {         
        if (e === elem)                        
            return true;                         
    }  

    return false; 
}

return isDisplayedInViewport(arguments[0]);
EOF;
}
