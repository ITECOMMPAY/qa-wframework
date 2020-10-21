<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 15:25
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;
use function count;


class SizeGreaterThanOrEqual extends CCond
{
    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->actualValue = count($facadeWebElements);

        $this->result = $this->actualValue >= $this->expectedValue;
    }

    public function __construct(string $conditionName, int $size)
    {
        parent::__construct($conditionName);

        $this->expectedValue = $size;
    }

    public function printExpectedValue() : string
    {
        return "коллекция содержит больше или равно $this->expectedValue элементов";
    }

    public function printActualValue() : string
    {
        return "коллекция содержит $this->actualValue элементов";
    }
}
