<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetTagName;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Tag extends AbstractCondition
{
    /**
     * @var string
     */
    protected $tagName;

    public function getName() : string
    {
        return "имеет тэг '$this->tagName'?";
    }

    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
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
        return $pageObject->accept(new GetTagName()) === $this->tagName;
    }
}
