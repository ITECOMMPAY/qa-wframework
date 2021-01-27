<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\TraverseFromRootExplanation;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Exist extends AbstractCondition
{
    public function getName() : string
    {
        return "присутствует в коде?";
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
        return !$collection->accept(new CountEmpty());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        return $pageObject->returnSeleniumElement()->isExist();
    }

    protected function getExplanationClasses() : array
    {
        return [TraverseFromRootExplanation::class];
    }
}
