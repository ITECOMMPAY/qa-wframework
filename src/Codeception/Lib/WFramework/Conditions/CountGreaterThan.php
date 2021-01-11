<?php


namespace Codeception\Lib\WFramework\Conditions;


class CountGreaterThan extends AbstractCondition
{
    /**
     * @var int
     */
    protected $size;

    public function getName() : string
    {
        return "содержит больше '$this->size' элементов?";
    }

    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function acceptWCollection($collection) : bool
    {
        return $collection->getElementsArray()->count() > $this->size;
    }
}