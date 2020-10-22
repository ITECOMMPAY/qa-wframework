<?php


namespace dodge\Helper\Elements\DodgeColorPicker\Inner;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Common\Module\WFramework\WebObjects\Primitive\WButton;
use Common\Module\WFramework\WebObjects\Primitive\WLabel;

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
