<?php


namespace dodge\Helper\Blocks;


use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use dodge\DodgeTester;
use dodge\_generated\Block\Operations;

abstract class DodgeBlock extends WBlock
{
    public function __construct(DodgeTester $actor)
    {
        parent::__construct($actor);
    }
    
    public function returnCodeceptionActor() : DodgeTester
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnOperations() : Operations
    {
        return $this->operations ?? $this->operations = new Operations($this);
    }
}