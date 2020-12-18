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
    protected $expectedValue;

    public function getName() : string
    {
        return "видимый текст соответствует '" . $this->expectedValue . "'? (с учётом регистра, без учёта пробелов)";
    }

    public function __construct(string $expectedText)
    {
        $this->expectedValue = trim(preg_replace(static::BLANK, ' ', $expectedText));
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
        return $this->expectedValue === trim(preg_replace(static::BLANK, ' ', $pageObject->accept(new GetText())));
    }
}
