<?php


namespace dodge\Helper\Blocks\VehiclePage;


use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\Basic\DodgeLabel;
use dodge\Helper\Steps\DodgeSteps;

class PricingBlock extends DodgeBlock
{

    protected function initName() : string
    {
        return "Блок цены со страницы авто";
    }

    protected function initPageLocator()
    {
        return "//div[@class='pricing-bar']";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
                        ->openSite()
                        ->closePopup()
                        ->openVehiclesMenu()
                        ->checkPrices()
                        ->selectVehicle('Alias: Challenger')
                        ->setZip()
                        ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->zipLabel = DodgeLabel::fromXpath(            'ZIP',           ".//div[@class='zipContainer']//div[@class='heading']/span");
        $this->buildAndPriceButton = DodgeButton::fromXpath('Build & Price', ".//a[@data-cats-id='Build & Price']");

        parent::__construct($actor);
    }








    public function getZipLabel() : DodgeLabel
    {
        return $this->zipLabel;
    }

    public function getBuildAndPriceButton() : DodgeButton
    {
        return $this->buildAndPriceButton;
    }
}
