<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class PageLoaded extends AbstractCondition
{
    public function getName() : string
    {
        return "страница загрузилась?";
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
        return (bool) $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_PAGE_LOADED));
    }

    protected const SCRIPT_PAGE_LOADED = "return document.readyState === 'complete';";
}
