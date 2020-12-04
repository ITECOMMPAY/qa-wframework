<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:53
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class AttributeValue extends Cond
{
    protected $attributeName = '';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->actualValue = $facadeWebElement
                                            ->get()
                                            ->attribute($this->attributeName)
                                            ;

        $this->result = $this->actualValue === $this->expectedValue;
    }

    public function __construct(string $conditionName, string $attributeName, string $expectedValue)
    {
        parent::__construct($conditionName);

        $this->attributeName = $attributeName;

        $this->expectedValue = $expectedValue;
    }

    public function printExpectedValue() : string
    {
        return "атрибут '$this->attributeName' должен иметь значение '$this->expectedValue'";
    }

    public function printActualValue() : string
    {
        if ($this->actualValue === null)
        {
            return "атрибут '$this->attributeName' отсутствует";
        }

        return "атрибут '$this->attributeName' имеет значение '$this->actualValue'";
    }
}
