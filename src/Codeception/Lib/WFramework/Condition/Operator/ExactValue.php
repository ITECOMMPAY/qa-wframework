<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 14:12
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


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
