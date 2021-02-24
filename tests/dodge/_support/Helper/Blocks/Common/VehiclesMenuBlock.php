<?php


namespace dodge\Helper\Blocks\Common;


use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Collections\DodgeCollection;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\DodgeVehicleCard\DodgeVehicleCard;
use dodge\Helper\Steps\DodgeSteps;

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
        $this->closeButton = DodgeButton::fromXpath('Close', ".//button[text()='close']");

        $this->cardsArray = DodgeCollection::fromFirstElement(DodgeVehicleCard::fromXpath('Карточка авто', ".//div[@data-cats-id='navcards']/div/div[@data-cats-id='grid']/div"));

        parent::__construct($actor);
    }



















    public function getCardsArray() : DodgeCollection
    {
        return $this->cardsArray;
    }

    public function getCloseButton() : DodgeButton
    {
        return $this->closeButton;
    }
}
