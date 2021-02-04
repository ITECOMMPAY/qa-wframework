<?php


namespace Codeception\Lib\WFramework\Conditions;


class CountLessThan extends AbstractCondition
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
        return "содержит меньше '$this->expected' элементов?";
    }

    public function __construct(int $count)
    {
        $this->expected = $count;
    }

    public function acceptWCollection($collection) : bool
    {
        $this->actual = $collection->getElementsArray()->count();

        return $this->actual < $this->expected;
    }
}
