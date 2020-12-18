<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetCssValue;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class CssPropertyWithValue extends AbstractCondition
{
    protected const BLANK = '/[\s\n\r\t\x{00a0}]+/m';

    /**
     * @var string
     */
    protected $property;

    /**
     * @var string
     */
    protected $expectedValue;

    public function getName() : string
    {
        return "CSS свойство '$this->property' имеет значение '$this->expectedValue'?";
    }

    public function __construct(string $property, string $expectedValue)
    {
        $this->property = $property;
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
                                                    preg_replace(static::BLANK, ' ', $pageObject->accept(new GetCssValue($this->property)))));
    }
}
