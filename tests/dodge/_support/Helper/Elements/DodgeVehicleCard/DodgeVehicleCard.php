<?php


namespace dodge\Helper\Elements\DodgeVehicleCard;


use Codeception\Lib\WFramework\Exceptions\Common\FrameworkStaledException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use Codeception\Lib\WFramework\WebObjects\Primitive\WLabel;

class DodgeVehicleCard extends WButton
{
    protected function initTypeName() : string
    {
        return "Карточка авто из меню Vehicle";
    }

    public function __construct(WFrom $importer)
    {
        $this->nameLabel  = WLabel::fromXpath('Название авто', ".//div/span[@data-cats-id='vehicle-name']");
        $this->_priceLabel = WLabel::fromXpath('Цена',          ".//div/span[@data-cats-id='price']/ins");

        parent::__construct($importer);
    }

    public function getVehicleName() : string
    {
        WLogger::logDebug($this . " -> получаем название авто");

        $name = $this->nameLabel->getCurrentValueString();

        WLogger::logDebug($this . " -> имеет название: $name");

        return $name;
    }

    public function hasPrice() : bool
    {
        WLogger::logDebug($this . " -> имеет цену?");

        return $this->_priceLabel->isExist();
    }

    public function _getPrice() : int
    {
        WLogger::logDebug($this . " -> получаем начальную цену авто");

        $price = $this->_priceLabel->getAllText();

        $parsedPrice = filter_var($price, FILTER_SANITIZE_NUMBER_INT);

        if ($parsedPrice === false)
        {
            throw new FrameworkStaledException('Не получилось распарсить цену авто: ' . $price);
        }

        WLogger::logDebug($this . " -> авто стоит: $parsedPrice долларов");

        return $parsedPrice;
    }
}
