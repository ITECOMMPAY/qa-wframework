<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 14:51
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class ExactText extends Cond
{
    protected $expectedValue = '';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = $facadeWebElement
                                            ->get()
                                            ->text()
                                            ;

        $this->result = $this->actualValue === $this->expectedValue;
    }

    public function __construct(string $conditionName, string $expectedText)
    {
        parent::__construct($conditionName);

        $this->expectedValue = $expectedText;
    }

    public function printExpectedValue() : string
    {
        return "видимый текст должен точно соответствовать '$this->expectedValue'";
    }

    public function printActualValue() : string
    {
        return $this->result ? "видимый текст точно соответствует '$this->expectedValue'" : "'$this->actualValue'";
    }
}
