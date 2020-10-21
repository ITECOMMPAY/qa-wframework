<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:43
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;


class Not extends CCond
{
    /** @var CCond  */
    protected $condition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        $this->condition->check($facadeWebElements);
        $this->result = !$this->condition->getResult();
    }

    public function __construct(CCond $condition)
    {
        parent::__construct('НЕ ' . $condition->getName());

        $this->condition = $condition;
    }

    public function printExpectedValue() : string
    {
        return 'НЕ ' . $this->condition->printExpectedValue();
    }

    public function printActualValue() : string
    {
        return 'НЕ ' . $this->condition->printActualValue();
    }
}
