<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:55
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class Disj extends Cond
{
    /** @var Cond[]  */
    protected $conditions = array();

    /** @var Cond  */
    protected $firstPassedCondition = null;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        foreach ($this->conditions as $condition)
        {
            if ($condition->check($facadeWebElement) === True)
            {
                $this->result = True;
                $this->firstPassedCondition = $condition;
                return;
            }
        }

        $this->result = False;
    }

    public function __construct(string $conditionName, Cond ...$conditions)
    {
        parent::__construct($conditionName);

        $this->conditions = $conditions;
    }

    public function printExpectedValue() : string
    {
        return 'хотя бы одно из условий должно выполняется';
    }

    public function printActualValue() : string
    {
        return $this->result ? $this->firstPassedCondition->toString() : 'ни одно из условий не выполнилось';
    }
}
