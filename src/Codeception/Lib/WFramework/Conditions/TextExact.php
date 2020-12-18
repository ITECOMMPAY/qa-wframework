<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextExact extends AbstractCondition
{
    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "видимый текст точно соответствует '" . $this->expectedValue . "'?";
    }

    public function __construct(string $expectedText)
    {
        $this->expectedValue = $expectedText;
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
        return $this->expectedValue === $pageObject->accept(new GetText());
    }
}
