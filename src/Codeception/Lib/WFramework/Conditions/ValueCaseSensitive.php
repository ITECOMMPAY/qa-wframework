<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class ValueCaseSensitive extends AbstractCondition
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
        return "содержит свойство value со значением '" . $this->expected . "'? (с учётом регистра, без учёта пробелов)";
    }

    public function __construct(string $expectedValue)
    {
        $this->expected = trim(preg_replace(static::BLANK, ' ', $expectedValue));
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
        $actualValue = $pageObject->accept(new GetValue());

        $this->actual = trim(preg_replace(static::BLANK, ' ', $actualValue));

        return $this->actual === $this->expected;
    }
}
