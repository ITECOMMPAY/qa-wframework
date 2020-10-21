<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 18:11
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;

class Explain extends CCond
{
    /** @var CCond  */
    protected $condition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->condition->check($facadeWebElements);

        $this->result = $this->condition->getResult();
    }

    public function __construct(CCond $condition, string $message)
    {
        parent::__construct($condition->getName() . ' (т.к. ' . $message . ')');

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
