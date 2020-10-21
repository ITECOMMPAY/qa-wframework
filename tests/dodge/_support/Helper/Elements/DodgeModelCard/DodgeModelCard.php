<?php


namespace dodge\Helper\Elements\DodgeModelCard;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Common\Module\WFramework\WebObjects\Primitive\WButton;
use Common\Module\WFramework\WebObjects\Primitive\WLabel;

class DodgeModelCard extends WButton
{

    protected function initTypeName() : string
    {
        return "Карточка модели авто";
    }

    public function __construct(WFrom $importer)
    {
        $this->title = WLabel::fromXpath('Название модели', ".//div[contains(@class, 'vehicle-title')]");

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
