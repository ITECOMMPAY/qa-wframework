<?php


namespace dodge\Helper\Elements\DodgeModelCard;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\Basic\DodgeLabel;

class DodgeModelCard extends DodgeButton
{

    protected function initTypeName() : string
    {
        return "Карточка модели авто";
    }

    public function __construct(WFrom $importer)
    {
        $this->title = DodgeLabel::fromXpath('Название модели', ".//div[contains(@class, 'vehicle-title')]");

        parent::__construct($importer);
    }

    public function getModelName() : string
    {
        WLogger::logDebug($this . " -> получаем название модели");

        $name = $this->title->getCurrentValueString();

        WLogger::logDebug($this . " -> имеет название: $name");

        return $name;
    }
}
