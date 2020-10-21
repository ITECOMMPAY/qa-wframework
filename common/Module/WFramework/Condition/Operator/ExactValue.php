<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 14:12
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class ExactValue extends Cond
{
    protected $expectedValue = '';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = $facadeWebElement->get()->value();

        $this->result = $this->actualValue === $this->expectedValue;
    }

    public function __construct(string $conditionName, string $expectedValue)
    {
        parent::__construct($conditionName);

        $this->expectedValue = $expectedValue;
    }

    public function printExpectedValue() : string
    {
        return "значение должно точно соответствовать '$this->expectedValue'";
    }

    public function printActualValue() : string
    {
        return $this->result ? "значение точно соответствует '$this->expectedValue'" : "'$this->actualValue'";
    }
}
