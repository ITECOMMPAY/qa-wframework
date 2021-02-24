<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
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

    public function why(IPageObject $pageObject, bool $actualValue = true) : string
    {
        return $this->getWrappedCondition()->why($pageObject, !$actualValue);
    }

    public function getWrappedCondition() : AbstractCondition
    {
        return $this->condition;
    }
}
