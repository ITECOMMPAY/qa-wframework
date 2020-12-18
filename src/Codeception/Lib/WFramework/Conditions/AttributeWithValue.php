<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttribute;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class AttributeWithValue extends AbstractCondition
{
    /**
     * @var string
     */
    protected $attributeName;

    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "атрибут '$this->attributeName' имеет значение '$this->expectedValue'?";
    }

    public function __construct(string $attributeName, string $expectedValue)
    {
        $this->attributeName = $attributeName;
        $this->expectedValue = $expectedValue;
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
        if ($collection->isEmpty())
        {
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        return $this->expectedValue === $pageObject->accept(new GetAttribute($this->attributeName));
    }
}
