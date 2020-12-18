<?php


namespace Codeception\Lib\WFramework\Operations\Check;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class CheckExists extends AbstractOperation
{
    public function getName() : string
    {
        return "проверяем, что существует в коде страницы";
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
        return $pageObject->accept(new Exist());
    }
}
