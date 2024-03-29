<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\Formatter\Why;
use Codeception\Lib\WFramework\Explanations\Result\TextExplanationResult;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class SomeElement_ extends AbstractCondition implements IWrapOtherCondition
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
        if ($collection->isEmpty())
        {
            return false;
        }

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

    public function why(IPageObject $pageObject, bool $actualValue = true) : Why
    {
        if (!$pageObject instanceof WCollection)
        {
            throw new UsageException($this . " -> должен применяться к WCollection");
        }

        if ($this->firstValidElement !== null)
        {
            return $this->getWrappedCondition()->why($this->firstValidElement, $actualValue);
        }

        if ($pageObject->isEmpty())
        {
            return new Why($pageObject . ' -> не содержит элементов');
        }

        return $this->getWrappedCondition()->why($pageObject->getFirstElement(), $actualValue);
    }

    protected function explainWhy(AbstractCondition $condition, IPageObject $pageObject, bool $actualValue) : array
    {
        if (!$pageObject instanceof WCollection)
        {
            throw new UsageException($this . " -> должен применяться к WCollection");
        }

        if ($this->firstValidElement !== null)
        {
            return parent::explainWhy($condition, $this->firstValidElement, $actualValue);
        }

        if ($pageObject->isEmpty())
        {
            return [new TextExplanationResult($pageObject . ' -> не содержит элементов')];
        }

        return parent::explainWhy($condition, $pageObject->getFirstElement(), $actualValue);
     }

    public function getWrappedCondition() : AbstractCondition
    {
        return $this->condition;
    }
}
