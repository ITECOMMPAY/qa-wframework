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
        return "содержит видимый текст который соответствует регулярке '" . $this->regex . "'?";
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

    protected function apply(WPageObject $pageObject) : bool
    {
        return (bool) preg_match($this->regex, $pageObject->accept(new GetText()));
    }
}
