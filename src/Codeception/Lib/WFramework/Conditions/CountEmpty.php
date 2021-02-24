<?php


namespace Codeception\Lib\WFramework\Conditions;


class CountEmpty extends AbstractCondition
{
    public function getName() : string
    {
        return "не содержит элементов?";
    }

    public function acceptWCollection($collection) : bool
    {
        return $collection->getElementsArray()->isEmpty();
    }
}
