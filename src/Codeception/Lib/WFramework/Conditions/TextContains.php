<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class TextContains extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "видимый текст содержит '" . $this->expectedValue . "'? (без учёта регистра и пробелов)";
    }

    public function __construct(string $expectedText)
    {
        $this->expectedValue = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $expectedText)));
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
        $actualValue = strtolower(
                                trim(
                                    preg_replace(static::BLANK, ' ', $pageObject->accept(new GetText()))));

        return false !== strpos($actualValue, $this->expectedValue);
    }
}
