<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 18:11
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


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
