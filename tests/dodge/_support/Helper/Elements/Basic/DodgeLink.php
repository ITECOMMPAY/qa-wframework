<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use dodge\Helper\Elements\DodgeElement;

class DodgeLink extends DodgeElement implements IHaveReadableText
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

    public function getFilteredText(string $regex, string $groupName = "") : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex, $groupName)
                    ;
    }
}