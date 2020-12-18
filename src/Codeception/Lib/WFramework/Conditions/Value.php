<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Value extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "значение свойства value соответствует '" . $this->expectedValue . "'? (без учёта регистра и пробелов)";
    }

    public function __construct(string $expectedValue)
    {
        $this->expectedValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedValue)));
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
        return $this->expectedValue === strtolower(
                                                trim(
                                                    preg_replace(static::BLANK, ' ', $pageObject->accept(new GetValue()))));
    }
}
