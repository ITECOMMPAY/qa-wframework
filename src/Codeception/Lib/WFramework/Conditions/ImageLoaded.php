<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class ImageLoaded extends AbstractCondition
{
    public function getName() : string
    {
        return "картинка загрузилась?";
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
        if (!$pageObject->accept(new Tag('img')))
        {
            return false;
        }

        return $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_IMAGE_LOADED));
    }

    protected const SCRIPT_IMAGE_LOADED = "return arguments[0].complete && typeof arguments[0].naturalWidth != 'undefined' && arguments[0].naturalWidth > 0;";
}
