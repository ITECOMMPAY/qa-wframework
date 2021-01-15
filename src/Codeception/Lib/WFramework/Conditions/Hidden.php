<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\TraverseFromRootExplanation;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Hidden extends AbstractCondition
{
    public function getName() : string
    {
        return "спрятан?";
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return true;
        }

        return $this->apply($collection->getFirstElement());
    }

    public function apply(WPageObject $pageObject)
    {
        return $pageObject->accept(new Not_(new Visible()));
    }

    protected function getExplanationClasses() : array
    {
        return [TraverseFromRootExplanation::class];
    }
}
