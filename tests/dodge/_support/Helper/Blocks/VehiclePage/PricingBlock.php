<?php


namespace dodge\Helper\Blocks\VehiclePage;

use Common\Module\WFramework\WebObjects\Primitive\WButton;
use Common\Module\WFramework\WebObjects\Primitive\WLabel;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\TestSteps\DodgeSteps;

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
        $this->zipLabel = WLabel::fromXpath(            'ZIP',           ".//div[@class='zipContainer']//div[@class='heading']/span");
        $this->buildAndPriceButton = WButton::fromXpath('Build & Price', ".//a[@data-cats-id='Build & Price']");

        parent::__construct($actor);
    }








    public function getZipLabel() : WLabel
    {
        return $this->zipLabel;
    }

    public function getBuildAndPriceButton() : WButton
    {
        return $this->buildAndPriceButton;
    }
}
