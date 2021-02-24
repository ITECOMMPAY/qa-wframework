<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttributesMap;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Map;

class Attribute extends AbstractCondition
{
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
        return "содержит атрибут '$this->expected'?";
    }

    public function __construct(string $name)
    {
        $this->expected = $name;
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

        return $this->actual->hasKey($this->expected);
    }
}
