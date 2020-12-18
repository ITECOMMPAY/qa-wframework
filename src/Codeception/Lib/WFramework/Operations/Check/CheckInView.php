<?php


namespace Codeception\Lib\WFramework\Operations\Check;


use Codeception\Lib\WFramework\Conditions\FullyVisible;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class CheckInView extends AbstractOperation
{
    public function getName() : string
    {
        return "проверяем, что целиком отображается в рамках экрана";
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
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : bool
    {
        return $pageObject->accept(new FullyVisible());
    }
}
