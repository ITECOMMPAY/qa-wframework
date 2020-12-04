<?php


namespace dodge\Helper\AliasMaps;


use Codeception\Lib\WFramework\AliasMap\AliasMap;

class ChallengerModelsMap extends AliasMap
{
    protected function getMap() : array
    {
        return [
            'Alias: SXT'                                        => 'SXT',
            'Alias: GT'                                         => 'GT',
            'Alias: R/T'                                        => 'R/T',
            'Alias: GT 50TH ANNIVERSARY'                        => 'GT 50TH ANNIVERSARY',
            'Alias: R/T SCAT PACK'                              => 'R/T SCAT PACK',
            'Alias: R/T 50TH ANNIVERSARY'                       => 'R/T 50TH ANNIVERSARY',
            'Alias: R/T SCAT PACK 50TH ANNIVERSARY'             => 'R/T SCAT PACK 50TH ANNIVERSARY',
            'Alias: R/T SCAT PACK WIDEBODY'                     => 'R/T SCAT PACK WIDEBODY',
            'Alias: R/T SCAT PACK WIDEBODY 50TH ANNIVERSARY'    => 'R/T SCAT PACK WIDEBODY 50TH ANNIVERSARY',
            'Alias: SRT HELLCAT'                                => 'SRT® HELLCAT',
            'Alias: SRT HELLCAT WIDEBODY'                       => 'SRT® HELLCAT WIDEBODY',
            'Alias: SRT HELLCAT REDEYE'                         => 'SRT® HELLCAT REDEYE',
            'Alias: SRT HELLCAT REDEYE WIDEBODY'                => 'SRT® HELLCAT REDEYE WIDEBODY',
        ];
    }
}
