<?php


namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use function preg_replace;
use function stripos;
use function strtolower;
use function trim;

class ValueContains extends Cond
{
    protected $expectedValue = '';

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = strtolower(
                                        trim(
                                             preg_replace(static::BLANK, ' ', $facadeWebElement->get()->value())));

        $this->result = stripos($this->actualValue, $this->expectedValue) !== False;
    }

    public function __construct(string $conditionName, string $expectedValue)
    {
        parent::__construct($conditionName);

        $this->expectedValue = strtolower(
            trim(
                preg_replace(static::BLANK, ' ', $expectedValue)));
    }

    public function printExpectedValue() : string
    {
        return "значение должно содержать '$this->expectedValue' (в любом регистре)";
    }

    public function printActualValue() : string
    {
        return $this->result ? "значение содержит '$this->expectedValue'" : "'$this->actualValue'";
    }
}
