<?php


namespace Codeception\Lib\WFramework\WebObjects\Primitive\Interfaces;


interface IMemorizeValue
{
    public function memorizeCurrentValue(string $propertiesKey = '');

    public function isHavingMemorizedValue(string $propertiesKey = '') : bool;
}
