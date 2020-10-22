<?php


namespace dodge\Helper\Blocks\BuildModelPage;


use Common\Module\WFramework\WebObjects\Primitive\WArray;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\DodgeOption\DodgeOption;
use dodge\Helper\TestSteps\DodgeSteps;

class StripesAndDecalsBlock extends DodgeBlock
{

    protected function initName() : string
    {
        return "Stripes & Decals";
    }

    protected function initPageLocator()
    {
        return "//div[contains(@class, 'sdp-configurator-exterior-equipment')]//h3[text()='Stripes & Decals']/ancestor::fieldset";
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
            ->selectBuyOption()
            ->selectModel('Alias: SRT HELLCAT REDEYE WIDEBODY')
        ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->decalsOptions = WArray::fromFirstElement(DodgeOption::fromXpath('Винил', ".//div[contains(@class, 'checkbox-group')]//div[contains(@class, 'sdp-form-checkbox ')]"));

        parent::__construct($actor);
    }









    public function getDecalsOptions() : WArray
    {
        return $this->decalsOptions;
    }
}
