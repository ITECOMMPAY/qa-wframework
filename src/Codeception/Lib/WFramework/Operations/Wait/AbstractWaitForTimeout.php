<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

abstract class AbstractWaitForTimeout extends AbstractOperation
{
    protected function getTimeout(IPageObject $pageObject) : int
    {
        return intdiv($pageObject->getTimeout(), $this->getDivisor());
    }

    abstract protected function getDivisor() : int;

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function acceptWCollection($collection)
    {
        $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject)
    {
        usleep($this->getTimeout($pageObject) * 1000000);
    }
}
