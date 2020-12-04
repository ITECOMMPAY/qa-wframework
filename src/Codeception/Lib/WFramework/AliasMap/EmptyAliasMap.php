<?php


namespace Codeception\Lib\WFramework\AliasMap;


class EmptyAliasMap extends AliasMap
{
    protected function getMap() : array
    {
        return [];
    }
}
