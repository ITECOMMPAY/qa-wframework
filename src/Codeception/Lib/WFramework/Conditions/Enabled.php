<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\TraverseFromRootExplanation;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Enabled extends AbstractCondition
{
    public function getName() : string
    {
        return "enabled?";
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
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        return $pageObject->returnSeleniumElement()->isEnabled();
    }

    protected function getExplanationClasses() : array
    {
        return [TraverseFromRootExplanation::class];
    }
}
