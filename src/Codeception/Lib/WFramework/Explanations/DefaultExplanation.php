<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Explanations\Result\DefaultExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\MissingValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class DefaultExplanation extends AbstractExplanation
{
    public function acceptWElement($element) : DefaultExplanationResult
    {
        return $this->apply($element);
    }

    public function acceptWBlock($block) : DefaultExplanationResult
    {
        return $this->apply($block);
    }

    public function acceptWCollection($collection) : DefaultExplanationResult
    {
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : DefaultExplanationResult
    {
        $expected = new MissingValue();
        $actual = new MissingValue();

        if (property_exists($this->condition, 'expected'))
        {
            $expected = $this->condition->expected;
        }

        if (property_exists($this->condition, 'actual'))
        {
            $actual = $this->condition->actual;
        }

        return new DefaultExplanationResult($this->conditionResult, $expected, $actual);
    }
}
