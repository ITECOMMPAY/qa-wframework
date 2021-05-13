<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetComputedStyleMap;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Map;

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
    public $expected;

    /**
     * @var Map
     */
    public $actual;

    public function getName() : string
    {
        return "содержит CSS свойство '$this->property' со значением '$this->expected'?";
    }

    public function __construct(string $property, string $value)
    {
        $this->property = $property;
        $this->expected = strtolower(
                                        trim(
                                            preg_replace(static::BLANK, ' ', $value)));
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
        $this->actual = $pageObject->accept(new GetComputedStyleMap());

        if (!$this->actual->hasKey($this->property))
        {
            return false;
        }

        $this->actual = $this->actual->get($this->property);

        return $this->expected === strtolower(
                                                trim(
                                                    preg_replace(static::BLANK, ' ',  $this->actual)));
    }
}
