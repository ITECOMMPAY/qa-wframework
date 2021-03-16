<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\Explanations\Formatter\Why;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResultAggregate;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class Not_ extends AbstractCondition implements IWrapOtherCondition
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    public function getName() : string
    {
        return 'НЕ ' . $this->condition->getName();
    }

    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
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
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : bool
    {
        return !$pageObject->accept($this->condition);
    }

    protected function explainWhy(AbstractCondition $condition, IPageObject $pageObject, bool $actualValue) : array
    {
        return parent::explainWhy($condition, $pageObject, !$actualValue);
    }

    public function getWrappedCondition() : AbstractCondition
    {
        return $this->condition;
    }
}
