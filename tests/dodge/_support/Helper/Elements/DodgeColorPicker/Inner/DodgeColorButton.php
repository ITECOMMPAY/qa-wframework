<?php


namespace dodge\Helper\Elements\DodgeColorPicker\Inner;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\Basic\DodgeLabel;

class DodgeColorButton extends DodgeButton
{
    protected function initTypeName() : string
    {
        return "Кнопка выбора цвета";
    }

    public function __construct(WFrom $importer)
    {
        $this->colorLabel = DodgeLabel::fromXpath('Название цвета', ".//label[contains(@title, 'Paint')]");

        parent::__construct($importer);
    }

    public function getColorName() : string
    {
        WLogger::logInfo($this, "получаем название цвета");

        return $this
                    ->colorLabel
                    ->returnOperations()
                    ->get()
                    ->attributeValue('data-lid')
                    ;
    }
}
