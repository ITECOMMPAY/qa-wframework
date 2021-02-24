<?php


namespace dodge\Helper\Blocks\Common;


use dodge\DodgeTester;
use dodge\Helper\AliasMaps\TabsHeaders\HeaderTabsMap;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\DodgeTabs\DodgeTabs;
use dodge\Helper\Steps\DodgeSteps;

class HeaderBlock extends DodgeBlock
{
    protected function initName() : string
    {
        return 'Блок хидера';
    }

    protected function initPageLocator()
    {
        return "//div[@id='main_navigation']";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
                                ->openSite()
                                ->closePopup()
                                ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->titleButton = DodgeButton::fromXpath('DODGE', ".//a[@data-lid='top-nav-top-nav-dodge-logo']");
        $this->tabs        = DodgeTabs::fromXpath('Панель вкладок', ".//ul")
                                                    ->setHeadersAliasMap(new HeaderTabsMap());
        $this->buildAndPriceButton = DodgeButton::fromXpath('Build & Price', ".//a[@data-lid='top-nav-build-and-price']");

        parent::__construct($actor);
    }




















    public function getBuildAndPriceButton() : DodgeButton
    {
        return $this->buildAndPriceButton;
    }

    public function getTabs() : DodgeTabs
    {
        return $this->tabs;
    }

    public function getTitleButton() : DodgeButton
    {
        return $this->titleButton;
    }
}
