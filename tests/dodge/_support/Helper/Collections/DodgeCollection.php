<?php


namespace dodge\Helper\Collections;


use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use dodge\DodgeTester;
use dodge\_generated\Collection\Operations;

abstract class DodgeCollection extends WCollection
{
    public function returnOperations() : Operations
    {
        return $this->operations ?? $this->operations = new Operations($this);
    }
}