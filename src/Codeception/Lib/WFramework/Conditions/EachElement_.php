<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class EachElement_ extends AbstractCondition implements IWrapOtherCondition
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

    public function acceptWCollection(WCollection $collection) : bool
    {
        if ($collection->isEmpty())
        {
            return false;
        }

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

    public function why(IPageObject $pageObject, bool $actualValue = true) : string
    {
        if (!$pageObject instanceof WCollection)
        {
            throw new UsageException($this . " -> должен применяться к WCollection");
        }

        if ($this->firstInvalidElement !== null)
        {
            return $this->getWrappedCondition()->why($this->firstInvalidElement, $actualValue);
        }

        if ($pageObject->isEmpty())
        {
            return $pageObject . ' -> не содержит элементов';
        }

        return $this->getWrappedCondition()->why($pageObject->getFirstElement(), $actualValue);
    }

    public function getWrappedCondition() : AbstractCondition
    {
        return $this->condition;
    }
}
