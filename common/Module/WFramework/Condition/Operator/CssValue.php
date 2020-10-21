<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:48
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class CssValue extends Cond
{
    protected $property = '';
    protected $expectedValue = '';

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $facadeWebElement->get()->cssValue($this->property))));

        $this->result = $this->actualValue === $this->expectedValue;
    }


    public function __construct(string $conditionName, string $property, string $expectedValue)
    {
        parent::__construct($conditionName);

        $this->property = $property;

        $this->expectedValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedValue)));
    }

    public function printExpectedValue() : string
    {
        return "свойство '$this->property' должно иметь значение '$this->expectedValue'";
    }

    public function printActualValue() : string
    {
        return "свойство '$this->property' имеет значение '$this->actualValue'";
    }
}
