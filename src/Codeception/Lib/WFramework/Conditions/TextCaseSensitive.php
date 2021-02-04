<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextCaseSensitive extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

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
        return "содержит видимый текст '" . $this->expected . "'? (с учётом регистра, без учёта пробелов)";
    }

    public function __construct(string $expectedText)
    {
        $this->expected = trim(preg_replace(static::BLANK, ' ', $expectedText));
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
        $actualText = $pageObject->accept(new GetText());

        $this->actual = trim(preg_replace(static::BLANK, ' ', $actualText));

        return $this->actual === $this->expected;
    }
}
