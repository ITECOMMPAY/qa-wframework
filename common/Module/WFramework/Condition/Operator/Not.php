<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:43
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class Not extends Cond
{
    protected $condition = null;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $this->condition->check($facadeWebElement);
        $this->result = !$this->condition->getResult();
    }

    public function __construct(Cond $condition)
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
        return $this->condition->printActualValue();
    }
}
