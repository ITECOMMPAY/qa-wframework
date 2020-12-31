<?php


namespace dodge\Helper\Blocks\FrontPage;


use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Steps\DodgeSteps;

class ChooseYourSiteBlock extends DodgeBlock
{
    protected function initName() : string
    {
        return 'Всплывающее окно Choose Your Site';
    }

    protected function initPageLocator()
    {
        return "//div[contains(@class, 'ISL-popup modal-ISL-overlay-BS')]";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
                            ->openSite()
                            ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->closeButton = DodgeButton::fromXpath('X', ".//button[string()='Close']");

        parent::__construct($actor);
    }













    public function getCloseButton() : DodgeButton
    {
        return $this->closeButton;
    }
}
