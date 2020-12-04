<?php


namespace dodge\Helper\Elements;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use dodge\DodgeTester;
use dodge\_generated\Element\Operations;

abstract class DodgeElement extends WElement
{
    public function returnCodeceptionActor() : DodgeTester
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnAdvanced() : Operations
    {
        return $this->advanced ?? $this->advanced = new Operations($this);
    }
}
