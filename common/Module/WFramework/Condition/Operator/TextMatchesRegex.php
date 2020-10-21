<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 15:13
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class TextMatchesRegex extends Cond
{
    protected $regex = '//';

    const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = $facadeWebElement->get()->text();

        $this->result = (bool) preg_match($this->regex, $this->actualValue);
    }

    public function __construct(string $conditionName, string $regex)
    {
        parent::__construct($conditionName);

        $this->regex = $regex;
    }

    public function printExpectedValue() : string
    {
        return "видимый текст должен соответствовать регулярке '$this->regex'";
    }

    public function printActualValue() : string
    {
        return $this->result ? "видимый текст соответствует регулярке '$this->regex'" : "'$this->actualValue'";
    }
}
