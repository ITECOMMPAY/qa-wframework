<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class SomeElement_ extends AbstractCondition
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    /**
     * @var WPageObject|null
     */
    protected $firstValidElement = null;

    public function getName() : string
    {
        return 'Хотя бы один элемент ' . $this->condition->getName();
    }

    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
    }

    public function acceptWCollection($collection) : bool
    {
        foreach ($collection->getElementsArray() as $element)
        {
            if ($element->accept($this->condition))
            {
                $this->firstValidElement = $element;
                return true;
            }
        }

        return false;
    }

    public function getExplanationClasses() : array
    {
        return $this->condition->getExplanationClasses();
    }

    public function why(IPageObject $pageObject, bool $actualValue = true) : array
    {
        if ($this->firstValidElement !== null)
        {
            return parent::why($this->firstValidElement, $actualValue);
        }

        return parent::why($pageObject, $actualValue);
    }
}
