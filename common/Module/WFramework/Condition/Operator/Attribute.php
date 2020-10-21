<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:51
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;

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
