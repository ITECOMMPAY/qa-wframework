<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 18:05
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;

class Delegate extends CCond
{
    protected $condition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->condition->check($facadeWebElements);

        $this->result = $this->condition->getResult();
    }

    public function __construct(string $delegateName, CCond $condition)
    {
        parent::__construct($delegateName . ' ' . $condition->getName());

        $this->condition = $condition;
    }

    public function printExpectedValue() : string
    {
        return $this->condition->printExpectedValue();
    }

    public function printActualValue() : string
    {
        return $this->condition->printActualValue();
    }
}
