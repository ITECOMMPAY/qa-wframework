<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Steps\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\Blocks\FrontPage\ChooseYourSiteBlock;
use dodge\Helper\Blocks\Common\HeaderBlock;

class FrontPageSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var HeaderBlock */
    public $headerBlock;

    /** @var ChooseYourSiteBlock */
    public $chooseYourSiteBlock;

    public function __construct(
        DodgeTester $I,
        HeaderBlock $headerBlock,
        ChooseYourSiteBlock $chooseYourSiteBlock
    )
    {
        $this->I = $I;
        $this->headerBlock = $headerBlock;
        $this->chooseYourSiteBlock = $chooseYourSiteBlock;
    }

    public function openSite() : FrontPageSteps
    {
        $this->I->logNotice($this, 'Открываем главную страницу сайта');

        $this->I->amOnPage('/');

        $this->setCookies();

        $this->shouldBeDisplayed();

        return $this;
    }

    protected function setCookies()
    {
        $this->I->setCookie('ISLPopUp',                     '1', ['domain' => 'www.dodge.com']);
        $this->I->setCookie('re-evaluation',                'false', ['domain' => 'www.dodge.com']);
        $this->I->setCookie('ipe.29665.pageViewedCount',    '1', ['domain' => 'www.dodge.com']);
        $this->I->setCookie('OptanonAlertBoxClosed',        '2020-10-20T14:25:46.159Z', ['domain' => '.dodge.com']);
        $this->I->setCookie('at_check',                     'true', ['domain' => '.dodge.com']);
        $this->I->setCookie('OptanonConsent',               'isIABGlobal=false&datestamp=Tue+Oct+20+2020+17%3A25%3A46+GMT%2B0300+(Moscow+Standard+Time)&version=6.7.0&hosts=&consentId=f4490550-e19d-4f21-8c55-a03a1c52a28d&interactionCount=1&landingPath=NotLandingPage&groups=C0001%3A1%2CC0002%3A1%2CC0003%3A1%2CC0004%3A1%2CC0005%3A1%2CBG14%3A1', ['domain' => '.dodge.com']);

    }

    public function shouldBeDisplayed() : FrontPageSteps
    {
        $this->I->logNotice($this, 'Проверяем, что главная страница отобразилась');

        $this->headerBlock->shouldBeDisplayed(true);

        return $this;
    }

    public function closePopup() : FrontPageSteps
    {
        $this->I->logNotice($this, 'Ожидаем всплывающее окно Choose Your Site и закрываем его');

        if ($this
                ->chooseYourSiteBlock
                ->finallyDisplayed())
        {
            $this
                ->chooseYourSiteBlock
                ->getCloseButton()
                ->click()
                ;

            $this
                ->chooseYourSiteBlock
                ->shouldBeHidden()
                ;
        }

        return $this;
    }

    public function openVehiclesMenu() : VehiclesMenuSteps
    {
        $this->I->logNotice($this, 'Открываем меню Vehicles');

        $this
            ->headerBlock
            ->getTabs()
            ->selectTab('Alias: Vehicles')
            ;

        return DodgeSteps::$vehiclesMenuSteps->shouldBeDisplayed();
    }

}
