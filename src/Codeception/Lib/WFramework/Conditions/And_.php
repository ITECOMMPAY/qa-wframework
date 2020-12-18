<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class And_ extends AbstractCondition
{
    /**
     * @var AbstractCondition[]
     */
    protected $conditions;

    /**
     * @var AbstractCondition|null
     */
    protected $firstFailedCondition = null;

    public function getName() : string
    {
        $names = ['все условия выполняются?: '];

        foreach ($this->conditions as $condition)
        {
            $names[] = $condition->getName();
        }

        return implode(PHP_EOL, $names);
    }

    public function __construct(AbstractCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    protected function apply(IPageObject $pageObject) : bool
    {
        foreach ($this->conditions as $condition)
        {
            if (false === $pageObject->accept($condition))
            {
                $this->firstFailedCondition = $condition;
                return false;
            }
        }

        return true;
    }

    public function getExplanationClasses() : array
    {
        return $this->firstFailedCondition->getExplanationClasses();
    }
}
