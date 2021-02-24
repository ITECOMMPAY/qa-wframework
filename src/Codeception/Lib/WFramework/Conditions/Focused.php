<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Focused extends AbstractCondition
{
    public function getName() : string
    {
        return "в фокусе?";
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
        return (bool) $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_FOCUSED));
    }

    protected const SCRIPT_FOCUSED = 'return document.activeElement === arguments[0];';
}
