<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
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

    /** @var PricingBlock */
    public $pricingBlock;

    public function __construct(
        DodgeTester $I,
        HeaderBlock $headerBlock,
        PricingBlock $pricingBlock
    )
    {
        $this->I = $I;
        $this->headerBlock = $headerBlock;
        $this->pricingBlock = $pricingBlock;
    }

    public function shouldBeDisplayed() : VehiclePageSteps
    {
        $this->I->logNotice($this, 'Проверяем, что страница авто отобразилась');

        $this->headerBlock->shouldBeDisplayed(true);

        return $this;
    }

    public function checkZip() : VehiclePageSteps
    {
        $expectedZip = TestProperties::mustGetValue('currentZip');

        $this->I->logNotice($this, "Проверяем, что ZIP: $expectedZip - был успешно задан");

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
        $this->I->logNotice($this, "Начинаем собирать авто");

        $this
            ->pricingBlock
            ->getBuildAndPriceButton()
            ->click()
            ;

        return DodgeSteps::$selectModelSteps->shouldBeDisplayed();
    }
}
