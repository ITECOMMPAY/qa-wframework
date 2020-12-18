<?php


namespace dodge\Helper\AliasMaps;


use Codeception\Lib\WFramework\AliasMaps\AliasMap;

class VehiclesNamesMap extends AliasMap
{
    protected function getMap() : array
    {
        return [
            'Alias: Charger'        => 'Charger',
            'Alias: Challenger'     => 'Challenger',
            'Alias: Durango'        => 'Durango',
            'Alias: Journey'        => 'Journey',
            'Alias: Grand Caravan'  => 'Grand Caravan'
        ];
    }
}
