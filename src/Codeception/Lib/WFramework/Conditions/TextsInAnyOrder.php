<?php


namespace Codeception\Lib\WFramework\Conditions;


class TextsInAnyOrder extends AbstractCondition
{
    /**
     * @var string[]
     */
    protected $texts;

    public function getName() : string
    {
        return "содержит строки: " . implode(', ', $this->texts) . " (без учёта регистра и пробелов) - в произвольном порядке?";
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

        foreach ($elements as $element)
        {
            foreach ($this->texts as $index => $text)
            {
                if (!$element->accept(new Text($text)))
                {
                    continue;
                }

                unset($this->texts[$index]);
                break;
            }
        }

        return empty($this->texts);
    }
}
