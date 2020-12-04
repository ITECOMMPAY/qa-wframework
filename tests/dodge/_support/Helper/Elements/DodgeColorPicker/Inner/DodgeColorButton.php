<?php


namespace dodge\Helper\Elements\DodgeColorPicker\Inner;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use Codeception\Lib\WFramework\WebObjects\Primitive\WLabel;

class DodgeColorButton extends WButton
{
    protected function initTypeName() : string
    {
        return "Кнопка выбора цвета";
    }

    public function __construct(WFrom $importer)
    {
        $this->colorLabel = WLabel::fromXpath('Название цвета', ".//label[contains(@title, 'Paint')]");

        parent::__construct($importer);
    }

    public function getColorName() : string
    {
        WLogger::logInfo($this . " -> получаем название цвета");

        return $this
                    ->colorLabel
                    ->returnSeleniumElement()
                    ->get()
                    ->attribute('data-lid')
                    ;
    }
}
