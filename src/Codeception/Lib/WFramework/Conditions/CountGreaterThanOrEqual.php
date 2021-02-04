<?php


namespace Codeception\Lib\WFramework\Conditions;


class CountGreaterThanOrEqual extends AbstractCondition
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
        return "содержит больше или равно '$this->expected' элементов?";
    }

    public function __construct(int $count)
    {
        $this->expected = $count;
    }

    public function acceptWCollection($collection) : bool
    {
        $this->actual = $collection->getElementsArray()->count();

        return $this->actual >= $this->expected;
    }
}
