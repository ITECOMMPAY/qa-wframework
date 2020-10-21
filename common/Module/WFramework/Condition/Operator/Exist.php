<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:21
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;

class Exist extends Cond
{
    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = $facadeWebElement->returnProxyWebElement()->isExist();
    }

    public function printExpectedValue() : string
    {
        return 'должен присутствовать';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'присутствует' : 'отсутствует';
    }
}
