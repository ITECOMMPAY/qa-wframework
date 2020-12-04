<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 15:16
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class TextContains extends Cond
{
    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = strtolower(
                                    trim(
                                        preg_replace(static::BLANK, ' ', $facadeWebElement->get()->text())));

        $this->result = strpos($this->actualValue, $this->expectedValue) !== False;
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
        return "видимый текст должен содержать текст '$this->expectedValue' (в любом регистре)";
    }

    public function printActualValue() : string
    {
        return $this->result ? "видимый текст содержит текст '$this->expectedValue'" : "'$this->actualValue'";
    }
}
