<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 02.04.19
 * Time: 15:04
 */

namespace Codeception\Lib\WFramework\CollectionCondition\Operator;

use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;

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
