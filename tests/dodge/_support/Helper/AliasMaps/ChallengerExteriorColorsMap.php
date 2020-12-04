<?php


namespace dodge\Helper\AliasMaps;


use Codeception\Lib\WFramework\AliasMap\AliasMap;

class ChallengerExteriorColorsMap extends AliasMap
{

    protected function getMap() : array
    {
        return [
            'Alias: Pitch Black'        => 'Pitch Black Exterior Paint',
            'Alias: Hellraisin'         => 'Hellraisin Exterior Paint',
            'Alias: Granite'            => 'Granite Exterior Paint',
            'Alias: Smoke Show'         => 'Smoke Show Exterior Paint',
            'Alias: Triple Nickel'      => 'Triple Nickel Exterior Paint',
            'Alias: IndiGo Blue'        => 'IndiGo Blue Exterior Paint',
            'Alias: Frostbite'          => 'Frostbite Exterior Paint',
            'Alias: Octane Red'         => 'Octane Red Exterior Paint',
            'Alias: TorRed'             => 'TorRed Exterior Paint',
            'Alias: Go Mango'           => 'Go Mango Exterior Paint',
            'Alias: Sinamon Stick'      => 'Sinamon Stick Exterior Paint',
            'Alias: F8 Green'           => 'F8 Green Exterior Paint',
            'Alias: White Knuckle'      => 'White Knuckle Exterior Paint',
        ];
    }
}
