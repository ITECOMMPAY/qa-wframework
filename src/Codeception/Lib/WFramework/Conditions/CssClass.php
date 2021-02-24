<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttributeValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class CssClass extends AbstractCondition
{
    /**
     * @var string
     */
    public $expected;

    /**
     * @var array
     */
    public $actual;

    public function getName() : string
    {
        return "содержит CSS класс '$this->expected'?";
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
        $classes = $pageObject->accept(new GetAttributeValue('class')) ?? '';

        $this->actual = explode(' ', $classes);

        return in_array($this->expected, $this->actual, true);
    }
}
