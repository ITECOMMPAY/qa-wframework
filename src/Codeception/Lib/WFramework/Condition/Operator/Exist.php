<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:21
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;

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
