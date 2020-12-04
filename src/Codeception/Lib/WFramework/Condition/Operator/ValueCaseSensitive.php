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


class ValueCaseSensitive extends Cond
{
    protected $expectedValue = '';

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = trim(preg_replace(static::BLANK, ' ', $facadeWebElement->get()->value()));

        $this->result = $this->actualValue === $this->expectedValue;
    }

    public function __construct(string $conditionName, string $expectedValue)
    {
        parent::__construct($conditionName);

        $this->expectedValue = trim(preg_replace(static::BLANK, ' ', $expectedValue));
    }

    public function printExpectedValue() : string
    {
        return "значение должно соответствовать '$this->expectedValue' (с учётом регистра)";
    }

    public function printActualValue() : string
    {
        return $this->result ? "значение соответствует '$this->expectedValue'" : "'$this->actualValue'";
    }
}
