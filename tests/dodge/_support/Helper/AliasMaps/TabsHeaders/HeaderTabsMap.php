<?php


namespace dodge\Helper\AliasMaps\TabsHeaders;


use Common\Module\WFramework\AliasMap\AliasMap;

class HeaderTabsMap extends AliasMap
{

    protected function getMap() : array
    {
        return [
            'Alias: Vehicles'       => 'Vehicles',
            'Alias: Shopping Tools' => 'SHOPPING TOOLS',
            'Alias: Dodge Garage'   => 'Dodge Garage(Open in a new window)',
            'Alias: Owners'         => 'OWNERS',
            'Alias: Merchandise'    => 'MERCHANDISE(Open in a new window)',
            'Alias: Dodge Muscle'   => 'DODGE MUSCLE',
        ];
    }
}
