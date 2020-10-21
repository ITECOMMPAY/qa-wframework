<?php


namespace dodge\Helper\Blocks;


use Common\Module\WFramework\WebObjects\Primitive\WArray;
use Common\Module\WFramework\WebObjects\Primitive\WButton;
use dodge\DodgeTester;
use dodge\Helper\Elements\DodgeVehicleCard\DodgeVehicleCard;
use dodge\Helper\TestSteps\DodgeSteps;

class VehiclesMenuBlock extends DodgeBlock
{
    protected function initName() : string
    {
        return 'Меню Vehicles';
    }

    protected function initPageLocator()
    {
        return "//div[@id='flyout-vehicles']";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
            ->openSite()
            ->closePopup()
            ->openVehiclesMenu()
            ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->closeButton = WButton::fromXpath('Close', ".//button[text()='close']");

        $this->cardsArray = WArray::fromFirstElement(DodgeVehicleCard::fromXpath('Карточка авто', ".//div[@data-cats-id='navcards']/div/div[@data-cats-id='grid']/div"));

        parent::__construct($actor);
    }



















    public function getCardsArray() : WArray
    {
        return $this->cardsArray;
    }

    public function getCloseButton() : WButton
    {
        return $this->closeButton;
    }
}
