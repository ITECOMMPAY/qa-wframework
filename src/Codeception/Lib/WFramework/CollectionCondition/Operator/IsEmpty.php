<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 15:24
 */

namespace Codeception\Lib\WFramework\CollectionCondition\Operator;


use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;
use function count;

class IsEmpty extends CCond
{
    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->result = empty($facadeWebElements->getElementsArray());

        $this->actualValue = count($facadeWebElements);
    }

    public function printExpectedValue() : string
    {
        return 'коллекция элементов должна быть пустая';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'коллекция элементов пустая' : "коллекция содержит $this->actualValue элементов";
    }
}
