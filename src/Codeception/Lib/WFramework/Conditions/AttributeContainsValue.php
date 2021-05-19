<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttributesMap;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Map;

class AttributeContainsValue extends AbstractCondition
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    public $expected;

    /**
     * @var Map
     */
    public $actual;

    public function getName() : string
    {
        return "содержит атрибут '$this->attribute' который включает в себя '$this->expected'?";
    }

    public function __construct(string $attribute, string $value)
    {
        $this->attribute = $attribute;
        $this->expected = $value;
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
        $this->actual = $pageObject->accept(new GetAttributesMap);

        if (!$this->actual->hasKey($this->attribute))
        {
            return false;
        }

        $this->actual = $this->actual->get($this->attribute);

        return strpos($this->actual, $this->expected) !== false;
    }
}
