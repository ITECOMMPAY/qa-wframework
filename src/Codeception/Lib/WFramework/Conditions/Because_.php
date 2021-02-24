<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class Because_ extends AbstractCondition implements IWrapOtherCondition
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    /**
     * @var string
     */
    protected $message;

    public function getName() : string
    {
        return $this->condition->getName() . ' т.к. ' . $this->message;
    }

    public function __construct(AbstractCondition $condition, string $message)
    {
        $this->condition = $condition;
        $this->message = $message;
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
        return $pageObject->accept($this->condition);
    }

    public function getWrappedCondition() : AbstractCondition
    {
        return $this->condition;
    }
}
