<?php


namespace dodge\Helper\TestSteps;


use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\StepsGroup\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\Blocks\VehiclePage\EnterYourZipBlock;
use dodge\Helper\Blocks\Common\HeaderBlock;
use dodge\Helper\Blocks\VehiclePage\PricingBlock;

class VehiclePageSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var HeaderBlock */
    public $headerBlock;

    /** @var EnterYourZipBlock */
    public $enterYourZipBlock;

    /** @var PricingBlock */
    public $pricingBlock;

    protected function _inject(
        DodgeTester $I,
        HeaderBlock $headerBlock,
        EnterYourZipBlock $enterYourZipBlock,
        PricingBlock $pricingBlock
    )
    {
        $this->I = $I;
        $this->headerBlock = $headerBlock;
        $this->enterYourZipBlock = $enterYourZipBlock;
        $this->pricingBlock = $pricingBlock;
    }

    public function shouldBeDisplayed() : VehiclePageSteps
    {
        $this->I->logNotice('Проверяем, что страница авто отобразилась');

        $this->headerBlock->shouldBeDisplayed();

        return $this;
    }

    public function setZip(string $zip = '85009') : VehiclePageSteps
    {
        $this->I->logNotice('Ожидаем всплывающее окно для ввода ZIP-кода и вводим в него: ' . $zip);

        $this
            ->enterYourZipBlock
            ->shouldBeDisplayed()
            ->getZipField()
            ->set($zip)
            ;

        $this
            ->enterYourZipBlock
            ->getSubmitButton()
            ->click()
            ;

        $this
            ->enterYourZipBlock
            ->shouldBeHidden()
            ;

        $this
            ->pricingBlock
            ->shouldBeDisplayed()
            ;

        TestProperties::setValue('currentZip', $zip);

        return $this;
    }

    public function checkZip() : VehiclePageSteps
    {
        $expectedZip = TestProperties::mustGetValue('currentZip');

        $this->I->logNotice("Проверяем, что ZIP: $expectedZip - был успешно задан");

        $actualZip = $this
                        ->pricingBlock
                        ->getZipLabel()
                        ->getCurrentValueString()
                        ;

        $this->I->assertEquals($expectedZip, $actualZip);

        return $this;
    }

    public function startBuildingModel() : SelectModelSteps
    {
        $this->I->logNotice("Начинаем собирать авто");

        $this
            ->pricingBlock
            ->getBuildAndPriceButton()
            ->click()
            ;

        return DodgeSteps::$selectModelSteps->shouldBeDisplayed();
    }
}
