<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetTagName;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Tag extends AbstractCondition
{
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
        return "содержит тэг '$this->expected'?";
    }

    public function __construct(string $name)
    {
        $this->expected = $name;
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
        $this->actual = $pageObject->accept(new GetTagName());

        return $this->actual === $this->expected;
    }
}
