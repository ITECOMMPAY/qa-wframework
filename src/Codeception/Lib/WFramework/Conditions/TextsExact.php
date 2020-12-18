<?php


namespace Codeception\Lib\WFramework\Conditions;


class TextsExact extends AbstractCondition
{
    /**
     * @var string[]
     */
    protected $texts;

    public function getName() : string
    {
        return "содержит строки: " . implode(', ', $this->texts) . " - в заданном порядке?";
    }

    public function __construct(string ...$texts)
    {
        $this->texts = array_values($texts);
    }

    public function acceptWCollection($collection) : bool
    {
        $elements = $collection->getElementsArray();

        if ($elements->count() < count($this->texts))
        {
            return false;
        }

        foreach ($elements as $index => $element)
        {
            if (!$element->accept(new TextExact($this->texts[$index])))
            {
                return false;
            }
        }

        return true;
    }
}
