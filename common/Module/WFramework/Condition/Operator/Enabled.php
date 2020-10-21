<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:54
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class Enabled extends Cond
{
    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = $facadeWebElement->returnProxyWebElement()->isEnabled();
    }

    public function printExpectedValue() : string
    {
        return 'должен быть enabled';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'enabled' : 'disabled';
    }
}
