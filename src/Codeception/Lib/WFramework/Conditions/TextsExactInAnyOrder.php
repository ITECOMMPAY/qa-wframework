<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;

class TextsExactInAnyOrder extends AbstractCondition
{
    /**
     * @var string[]
     */
    public $expected;

    /**
     * @var string[]
     */
    public $actual;

    public function getName() : string
    {
        return "содержит строки: " . implode(', ', $this->expected) . " - в произвольном порядке?";
    }

    public function __construct(string ...$texts)
    {
        $this->expected = array_values($texts);
    }

    public function acceptWCollection($collection) : bool
    {
        $elements = $collection->getElementsArray();

        $this->actual = $collection->accept(new GetText());

        if ($elements->count() < count($this->expected))
        {
            return false;
        }

        $remainingTexts = $this->expected;

        foreach ($elements as $element)
        {
            foreach ($this->expected as $index => $text)
            {
                if (!$element->accept(new TextExact($text)))
                {
                    continue;
                }

                unset($remainingTexts[$index]);
                break;
            }
        }

        return empty($remainingTexts);
    }
}
