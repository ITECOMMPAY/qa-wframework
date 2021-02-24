<?php


namespace Codeception\Lib\WFramework\Operations\Check;


use Codeception\Lib\WFramework\Conditions\Enabled;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class CheckEnabled extends AbstractOperation
{
    public function getName() : string
    {
        return "проверяем, что доступен для взаимодействия";
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    protected function apply(IPageObject $pageObject) : bool
    {
        return $pageObject->accept(new Enabled());
    }
}
