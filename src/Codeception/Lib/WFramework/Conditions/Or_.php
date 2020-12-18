<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class Or_ extends AbstractCondition
{
    /**
     * @var AbstractCondition[]
     */
    protected $conditions;

    /**
     * @var AbstractCondition|null
     */
    protected $firstPassedCondition = null;

    public function getName() : string
    {
        $names = ['хотя бы одно из условий выполняется?: '];

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
        foreach ($this->conditions as $condition)
        {
            if (true === $pageObject->accept($condition))
            {
                $this->firstPassedCondition = $condition;
                return true;
            }
        }

        return false;
    }

    public function getExplanationClasses() : array
    {
        return $this->firstPassedCondition->getExplanationClasses();
    }

}
