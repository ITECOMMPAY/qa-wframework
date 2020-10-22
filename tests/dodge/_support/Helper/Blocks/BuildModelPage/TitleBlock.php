<?php


namespace dodge\Helper\Blocks\BuildModelPage;


use Common\Module\WFramework\WebObjects\Primitive\WLabel;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\TestSteps\DodgeSteps;

class TitleBlock extends DodgeBlock
{

    protected function initName() : string
    {
        return "Блок с названием модели";
    }

    protected function initPageLocator()
    {
        return "//div[contains(@class, 'sdp-configurator-exterior-equipment')]";
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
        $this->titleLabel = WLabel::fromXpath('Название модели', ".//h2[text()='Exterior']/preceding-sibling::h1/span");

        parent::__construct($actor);
    }













    public function getTitleLabel() : WLabel
    {
        return $this->titleLabel;
    }
}
