<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 15:25
 */

namespace Codeception\Lib\WFramework\CollectionCondition\Operator;


use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;
use function count;


class SizeLessThan extends CCond
{
    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->actualValue = count($facadeWebElements);

        $this->result = $this->actualValue < $this->expectedValue;
    }

    public function __construct(string $conditionName, int $size)
    {
        parent::__construct($conditionName);

        $this->expectedValue = $size;
    }

    public function printExpectedValue() : string
    {
        return "коллекция содержит меньше $this->expectedValue элементов";
    }

    public function printActualValue() : string
    {
        return "коллекция содержит $this->actualValue элементов";
    }
}
