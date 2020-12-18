<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextMatchesRegex extends AbstractCondition
{
    /**
     * @var string
     */
    protected $regex;

    public function getName() : string
    {
        return "видимый текст соответствует регулярке '" . $this->regex . "'?";
    }

    public function __construct(string $regex)
    {
        $this->regex = $regex;
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
        return (bool) preg_match($this->regex, $pageObject->accept(new GetText()));
    }
}
