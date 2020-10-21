<?php


namespace dodge\Helper\AliasMaps;


use Common\Module\WFramework\AliasMap\AliasMap;

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
