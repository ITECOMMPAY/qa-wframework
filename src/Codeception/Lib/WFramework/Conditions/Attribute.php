<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttribute;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Attribute extends AbstractCondition
{
    /**
     * @var string
     */
    protected $attributeName;

    public function getName() : string
    {
        return "атрибут '$this->attributeName' присутствует?";
    }

    public function __construct(string $attributeName)
    {
        $this->attributeName = $attributeName;
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
        return null !== $pageObject->accept(new GetAttribute($this->attributeName));
    }
}
