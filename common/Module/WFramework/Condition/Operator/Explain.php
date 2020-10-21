<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 18:11
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class Explain extends Cond
{
    protected $condition = null;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->condition->check($facadeWebElement);

        $this->result = $this->condition->getResult();
    }

    public function __construct(Cond $condition, string $message)
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
