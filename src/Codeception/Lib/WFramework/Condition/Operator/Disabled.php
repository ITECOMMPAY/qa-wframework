<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:55
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class Disabled extends Cond
{

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = !$facadeWebElement->returnProxyWebElement()->isEnabled();
    }

    public function printExpectedValue() : string
    {
        return 'должен быть disabled';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'disabled' : 'enabled';
    }
}
