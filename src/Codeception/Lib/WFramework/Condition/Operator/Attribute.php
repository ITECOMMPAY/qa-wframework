<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:51
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;

class Attribute extends Cond
{
    protected $attributeName = '';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->result = null !== $facadeWebElement
                                                    ->get()
                                                    ->attribute($this->attributeName)
                                                    ;
    }

    public function __construct(string $conditionName, string $attributeName)
    {
        parent::__construct($conditionName);

        $this->attributeName = $attributeName;
    }

    public function printExpectedValue() : string
    {
        return "атрибут '$this->attributeName' должен присутствовать";
    }

    public function printActualValue() : string
    {
        return $this->result ? "атрибут '$this->attributeName' присутствует" : "атрибут '$this->attributeName' отсутствует";
    }
}
