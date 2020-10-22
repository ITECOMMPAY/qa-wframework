<?php


namespace dodge\Helper\Blocks\BuildModelPage;


use dodge\DodgeTester;
use dodge\Helper\AliasMaps\ChallengerExteriorColorsMap;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\DodgeColorPicker\DodgeColorPicker;
use dodge\Helper\TestSteps\DodgeSteps;

class ExteriorColorBlock extends DodgeBlock
{

    protected function initName() : string
    {
        return "Блок выбора наружного цвета авто";
    }

    protected function initPageLocator()
    {
        return "//div[contains(@class, 'sdp-configurator-exterior-equipment')]//h3[text()='Exterior Colors']/ancestor::fieldset";
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
        $this->colorPicker = DodgeColorPicker::fromXpath('Панель выбора цвета', ".//div[contains(@class, 'radio-group')]")
                                                    ->setColorsAliasMap(new ChallengerExteriorColorsMap());

        parent::__construct($actor);
    }












    public function getColorPicker() : DodgeColorPicker
    {
        return $this->colorPicker;
    }
}
