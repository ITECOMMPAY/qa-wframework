<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


class DefaultExplanationResult extends AbstractExplanationResult
{
    protected $conditionResult;
    protected $expectedValue;
    protected $actualValue;

    public function __construct(bool $conditionResult, $expectedValue = null, $actualValue = null)
    {
        parent::__construct();

        $this->conditionResult = $conditionResult;
        $this->expectedValue = $expectedValue;
        $this->actualValue = $actualValue;
    }

    public function getConditionResult() : bool
    {
        return $this->conditionResult;
    }

    /**
     * @return mixed
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }

    /**
     * @return mixed
     */
    public function getActualValue()
    {
        return $this->actualValue;
    }
}