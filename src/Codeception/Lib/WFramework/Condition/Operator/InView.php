<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 25.04.19
 * Time: 13:42
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;

class InView extends Cond
{
    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = $facadeWebElement
                                    ->returnProxyWebElement()
                                    ->executeScriptOnThis(static::SCRIPT_IN_VIEW)
                                    ;
    }

    public function printExpectedValue() : string
    {
        return "должен отображаться на экране";
    }

    public function printActualValue() : string
    {
        return $this->result ? 'отображается' : 'не отображается';
    }

    const SCRIPT_IN_VIEW = <<<EOF
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
