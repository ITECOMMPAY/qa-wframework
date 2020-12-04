<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:47
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;


class Conj extends Cond
{
    /** @var Cond[]  */
    protected $conditions = array();

    /** @var Cond  */
    protected $firstFailedCondition = null;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        foreach ($this->conditions as $condition)
        {
            if ($condition->check($facadeWebElement) === False)
            {
                $this->firstFailedCondition = $condition;
                $this->result = False;
                return;
            }
        }

        $this->result = True;
    }

    public function __construct(string $conditionName, Cond ...$conditions)
    {
        parent::__construct($conditionName);

        $this->conditions = $conditions;
    }

    public function printExpectedValue() : string
    {
        return 'все условия должны выполняться';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'все условия выполняются' : $this->firstFailedCondition->toString();
    }

}
