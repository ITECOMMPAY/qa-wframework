<?php


namespace Codeception\Lib\WFramework\Conditions;


class CountGreaterThan extends AbstractCondition
{
    /**
     * @var int
     */
    public $expected;

    /**
     * @var int
     */
    public $actual;

    public function getName() : string
    {
        return "содержит больше '$this->expected' элементов?";
    }

    public function __construct(int $expectedValue)
    {
        $this->expected = $expectedValue;
    }

    public function acceptWCollection($collection) : bool
    {
        $this->actual = $collection->getElementsArray()->count();

        return $this->actual > $this->expected;
    }
}
