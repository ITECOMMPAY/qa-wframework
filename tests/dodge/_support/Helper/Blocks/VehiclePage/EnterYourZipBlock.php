<?php


namespace dodge\Helper\Blocks\VehiclePage;


use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use Codeception\Lib\WFramework\WebObjects\Primitive\WTextBox;
use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\TestSteps\DodgeSteps;

class EnterYourZipBlock extends DodgeBlock
{
    protected function initName() : string
    {
        return 'Всплывающее окно ввода ZIP-кода';
    }

    protected function initPageLocator()
    {
        return "//div[@id='modal' and @aria-expanded='true']";
    }

    protected function openPage()
    {
        DodgeSteps::$frontPageSteps
                        ->openSite()
                        ->closePopup()
                        ->openVehiclesMenu()
                        ->selectVehicle('Alias: Challenger')
                        ;
    }

    public function __construct(DodgeTester $actor)
    {
        $this->zipField      = WTextBox::fromXpath('Enter ZIP Code', ".//input");
        $this->submitButton  = WButton::fromXpath( '>',              ".//a");

        parent::__construct($actor);
    }















    public function getSubmitButton() : WButton
    {
        return $this->submitButton;
    }

    public function getZipField() : WTextBox
    {
        return $this->zipField;
    }
}
