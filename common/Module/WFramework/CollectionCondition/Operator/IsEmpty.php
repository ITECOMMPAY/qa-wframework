<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 15:24
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;
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
