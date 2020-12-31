<?php


namespace dodge\Helper\Blocks\SelectModelPage;


use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Collections\DodgeCollection;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\DodgeModelCard\DodgeModelCard;
use dodge\Helper\Steps\DodgeSteps;

class SelectModelBlock extends DodgeBlock
{
    protected function initName() : string
    {
        return "Блок выбора модели авто";
    }

    protected function initPageLocator()
    {
        return "//div[@data-cats-id='sdp-column' and contains(@class, 'sdp-configurator')]";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
                        ->openSite()
                        ->closePopup()
                        ->openVehiclesMenu()
                        ->selectVehicle('Alias: Challenger')
                        ->setZip()
                        ->startBuildingModel()
                        ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->buyButton   = DodgeButton::fromXpath('Buy',   ".//a[text()='buy']");
        $this->leaseButton = DodgeButton::fromXpath('Lease', ".//a[text()='lease']");

        $this->modelsArray = DodgeCollection::fromFirstElement(DodgeModelCard::fromXpath('Карточка модели авто', ".//div[contains(@class, 'grid')]//div[contains(@class, 'sdp-col')]"));

        parent::__construct($actor);
    }















    public function getBuyButton() : DodgeButton
    {
        return $this->buyButton;
    }

    public function getLeaseButton() : DodgeButton
    {
        return $this->leaseButton;
    }

    public function getModelsArray() : DodgeCollection
    {
        return $this->modelsArray;
    }
}
