<?php


namespace dodge\Helper\Blocks\VehiclePage;


use dodge\DodgeTester;
use dodge\Helper\Blocks\DodgeBlock;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\Basic\DodgeTextBox;
use dodge\Helper\Steps\DodgeSteps;

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
        $this->zipField      = DodgeTextBox::fromXpath('Enter ZIP Code', ".//input");
        $this->submitButton  = DodgeButton::fromXpath( '>',              ".//a");

        parent::__construct($actor);
    }















    public function getSubmitButton() : DodgeButton
    {
        return $this->submitButton;
    }

    public function getZipField() : DodgeTextBox
    {
        return $this->zipField;
    }
}
