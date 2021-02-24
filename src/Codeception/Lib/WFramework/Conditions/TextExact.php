<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextExact extends AbstractCondition
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
        return "содержит текст который точно соответствует '" . $this->expected . "'?";
    }

    public function __construct(string $expectedText)
    {
        $this->expected = $expectedText;
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
        $this->actual = $pageObject->accept(new GetText());

        return $this->actual === $this->expected;
    }
}
