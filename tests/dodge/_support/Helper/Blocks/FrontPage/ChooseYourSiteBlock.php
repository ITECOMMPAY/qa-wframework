<?php


namespace dodge\Helper\Blocks\FrontPage;


use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\TestSteps\DodgeSteps;

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
        $this->closeButton = WButton::fromXpath('X', ".//button[string()='Close']");

        parent::__construct($actor);
    }













    public function getCloseButton() : WButton
    {
        return $this->closeButton;
    }
}
