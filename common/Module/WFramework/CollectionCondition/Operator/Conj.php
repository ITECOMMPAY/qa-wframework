<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 17:47
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;

class Conj extends CCond
{
    /** @var CCond[]  */
    protected $conditions = array();

    /** @var CCond  */
    protected $firstFailedCondition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        foreach ($this->conditions as $condition)
        {
            if ($condition->check($facadeWebElements) === False)
            {
                $this->firstFailedCondition = $condition;
                $this->result = False;
            }
        }

        $this->result = True;
    }

    public function __construct(string $conditionName, CCond ...$conditions)
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
