<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:55
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class Selected extends Cond
{
    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = $facadeWebElement->returnProxyWebElement()->isSelected();
    }

    public function printExpectedValue() : string
    {
        return 'должен быть выбран';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'выбран' : 'не выбран';
    }
}
