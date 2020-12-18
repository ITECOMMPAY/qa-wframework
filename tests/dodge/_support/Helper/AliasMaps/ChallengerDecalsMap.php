<?php


namespace dodge\Helper\AliasMaps;


use Codeception\Lib\WFramework\AliasMaps\AliasMap;

class ChallengerDecalsMap extends AliasMap
{
    protected function getMap() : array
    {
        return [
            'Alias: Blue Dual Stripe'                     => 'Blue Dual Stripe',
            'Alias: Dual Carbon Stripes'                  => 'Dual Carbon Stripes',
            'Alias: Dual Gunmetal Stripes'                => 'Dual Gunmetal Stripes',
            'Alias: Dual Silver Stripes'                  => 'Dual Silver Stripes',
            'Alias: Dual Red Stripes'                     => 'Dual Red Stripes',
            'Alias: Painted Satin Black Graphics Package' => 'Painted Satin Black Graphics Package',
            'Alias: Satin Black Painted Hood'             => 'Satin Black Painted Hood',
            'Alias: Satin Black Painted Roof'             => 'Satin Black Painted Roof',
        ];
    }
}
