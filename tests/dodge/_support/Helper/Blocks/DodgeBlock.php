<?php


namespace dodge\Helper\Blocks;


use Common\Module\WFramework\WebObjects\Base\WBlock\WBlock;
use dodge\DodgeTester;

abstract class DodgeBlock extends WBlock
{
    public function __construct(DodgeTester $actor)
    {
        parent::__construct($actor);
    }
}
