<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 15:06
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class SelectedText extends Cond
{
    protected $expectedText;

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    const SCRIPT = "return arguments[0].value.substring(arguments[0].selectionStart, arguments[0].selectionEnd);";

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $selectedText = (string) $facadeWebElement
                                                ->exec()
                                                ->scriptOnThis(static::SCRIPT)
                                                ;

        $this->actualValue = strtolower(trim(preg_replace(static::BLANK, ' ', $selectedText)));

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
        return "выделенный текст должен соответствовать '$this->expectedValue' (в любом регистре)";
    }

    public function printActualValue() : string
    {
        return $this->result ? "выделенный текст соответствует '$this->expectedValue'" : "'$this->actualValue'";
    }
}
