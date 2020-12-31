<?php


namespace dodge\Helper\Elements\Basic;


use dodge\Helper\Elements\DodgeElement;

class DodgeLink extends DodgeElement
{
    protected function initTypeName() : string
    {
        return 'Ссылка на файл';
    }

    public function download() : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->file()
                    ;
    }
}