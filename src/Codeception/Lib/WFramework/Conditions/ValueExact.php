<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class ValueExact extends AbstractCondition
{
    /**
     * @var string
     */
    public $expected;

    /**
     * @var string
     */
    public $actual;

    public function getName() : string
    {
        return "содержит свойство value с точным значением '" . $this->expected . "'?";
    }

    public function __construct(string $expectedValue)
    {
        $this->expected = $expectedValue;
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $this->actual = $pageObject->accept(new GetValue());

        return $this->actual === $this->expected;
    }
}
