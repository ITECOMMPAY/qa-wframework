<?php


namespace dodge\Helper\Blocks\SelectModelPage;


use Codeception\Lib\WFramework\WebObjects\Primitive\WArray;
use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\DodgeModelCard\DodgeModelCard;
use dodge\Helper\TestSteps\DodgeSteps;

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
        $this->buyButton   = WButton::fromXpath('Buy',   ".//a[text()='buy']");
        $this->leaseButton = WButton::fromXpath('Lease', ".//a[text()='lease']");

        $this->modelsArray = WArray::fromFirstElement(DodgeModelCard::fromXpath('Карточка модели авто', ".//div[contains(@class, 'grid')]//div[contains(@class, 'sdp-col')]"));

        parent::__construct($actor);
    }















    public function getBuyButton() : WButton
    {
        return $this->buyButton;
    }

    public function getLeaseButton() : WButton
    {
        return $this->leaseButton;
    }

    public function getModelsArray() : WArray
    {
        return $this->modelsArray;
    }
}
