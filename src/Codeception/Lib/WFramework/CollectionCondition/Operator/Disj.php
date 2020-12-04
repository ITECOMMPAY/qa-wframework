<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:55
 */

namespace Codeception\Lib\WFramework\CollectionCondition\Operator;


use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;

class Disj extends CCond
{
    /** @var CCond[]  */
    protected $conditions = array();

    /** @var CCond  */
    protected $firstPassedCondition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        foreach ($this->conditions as $condition)
        {
            if ($condition->check($facadeWebElements) === True)
            {
                $this->result = True;
                $this->firstPassedCondition = $condition;
                return;
            }
        }

        $this->result = False;
    }

    public function __construct(string $conditionName, CCond ...$conditions)
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
