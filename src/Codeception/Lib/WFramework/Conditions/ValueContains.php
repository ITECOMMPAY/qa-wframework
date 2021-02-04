<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class ValueContains extends AbstractCondition
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
        return "содержит свойство value с подстрокой '" . $this->expected . "'? (без учёта регистра и пробелов)";
    }

    public function __construct(string $expectedValue)
    {
        $this->expected = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedValue)));
    }

    public function getExpected() : string
    {
        return $this->expected;
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
        $this->actual = strtolower(
                                trim(
                                    preg_replace(static::BLANK, ' ', $pageObject->accept(new GetValue()))));

        return false !== strpos($this->actual, $this->expected);
    }
}
