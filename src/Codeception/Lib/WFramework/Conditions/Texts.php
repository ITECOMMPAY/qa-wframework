<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;

class Texts extends AbstractCondition
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
        return "содержит строки: " . implode(', ', $this->expected) . " (без учёта регистра и пробелов) - в заданном порядке?";
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

        foreach ($elements as $index => $element)
        {
            if (!$element->accept(new Text($this->expected[$index])))
            {
                return false;
            }
        }

        return true;
    }
}
