<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class EachElement_ extends AbstractCondition
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    /**
     * @var WPageObject|null
     */
    protected $firstInvalidElement = null;

    public function getName() : string
    {
        return 'Каждый элемент ' . $this->condition->getName();
    }

    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
    }

    public function acceptWCollection($collection) : bool
    {
        foreach ($collection->getElementsArray() as $element)
        {
            if (!$element->accept($this->condition))
            {
                $this->firstInvalidElement = $element;
                return false;
            }
        }

        return true;
    }

    public function getExplanationClasses() : array
    {
        return $this->condition->getExplanationClasses();
    }

    public function why(IPageObject $pageObject, bool $actualValue = true) : string
    {
        if ($this->firstInvalidElement !== null)
        {
            return parent::why($this->firstInvalidElement, $actualValue);
        }

        return parent::why($pageObject, $actualValue);
    }
}
