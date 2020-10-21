<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:52
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class Focused extends Cond
{
    const SCRIPT = "return document.activeElement;";

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $focusedElement = $facadeWebElement
                                        ->exec()
                                        ->script(static::SCRIPT)
                                        ;

        $this->result = $facadeWebElement->returnProxyWebElement()->equals($focusedElement);
    }

    public function printExpectedValue() : string
    {
        return 'фокус должен находится на заданном элементе';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'фокус находится на заданном элементе' : 'фокус находится на другом элементе' ;
    }
}
