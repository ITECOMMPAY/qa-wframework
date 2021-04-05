<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use dodge\Helper\Elements\DodgeElement;

class DodgeLabel extends DodgeElement implements IHaveReadableText, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Текстовый элемент';
    }

    public function getFilteredText(string $regex, string $groupName = "") : string
    {
        WLogger::logAction($this, "получаем надпись отфильтрованную по регулярке: $regex");

        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex)
                    ;
    }

    public function getCurrentValueString() : string
    {
        return $this->getAllText();
    }
}