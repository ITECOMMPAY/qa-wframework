<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 02.04.19
 * Time: 15:04
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;

use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;

class EveryElement extends CCond
{
    protected $condition = null;

    protected function apply(FacadeWebElements $facadeWebElements)
    {
        foreach ($facadeWebElements as $facadeWebElement)
        {
            if ($facadeWebElement
                                ->checkIt()
                                ->isNot($this->condition))
            {
                $this->result = False;
                return;
            }
        }

        $this->result = True;
    }

    public function __construct(string $conditionName, Cond $condition)
    {
        parent::__construct($conditionName . ' ' . $condition->getName());

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
