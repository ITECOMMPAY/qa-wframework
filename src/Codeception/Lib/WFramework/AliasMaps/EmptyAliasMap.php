<?php


namespace Codeception\Lib\WFramework\AliasMaps;


class EmptyAliasMap extends AliasMap
{
    protected function getMap() : array
    {
        return [];
    }
}
