<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 14:51
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class Text extends Cond
{
    protected $expectedText = '';

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $facadeWebElement->get()->text())));

        $this->result = $this->actualValue === $this->expectedValue;
    }


    public function __construct(string $conditionName, string $expectedText)
    {
        parent::__construct($conditionName);

        $this->expectedValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedText)));
    }

    public function printExpectedValue() : string
    {
        return "видимый текст должен соответствовать '$this->expectedValue' (в любом регистре)";
    }

    public function printActualValue() : string
    {
        return $this->result ? "видимый текст соответствует '$this->expectedValue'" : "'$this->actualValue'";
    }
}
