<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Conditions\Hidden;

class HiddenExplanation extends TraverseFromRootExplanation
{
    public function __construct(AbstractCondition $condition, bool $conditionResult = true)
    {
        parent::__construct($condition, $conditionResult);

        $this->condition = new Hidden();
    }
}