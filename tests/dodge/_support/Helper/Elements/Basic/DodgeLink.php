<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\Logger\WLogger;
use dodge\Helper\Elements\DodgeElement;

class DodgeLink extends DodgeElement
{
    protected function initTypeName() : string
    {
        return 'Ссылка на файл';
    }

    public function download() : string
    {
        WLogger::logAction($this, "скачиваем файл");

        return $this
                    ->returnOperations()
                    ->get()
                    ->file()
                    ;
    }
}