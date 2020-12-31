<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\AliasMaps\VehiclesNamesMap;
use dodge\Helper\Blocks\Common\VehiclesMenuBlock;
use dodge\Helper\Elements\DodgeVehicleCard\DodgeVehicleCard;

class VehiclesMenuSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var VehiclesMenuBlock */
    public $vehiclesMenuBlock;

    /** @var VehiclesNamesMap */
    public $vehiclesNamesMap;

    public function __construct(
        DodgeTester $I,
        VehiclesMenuBlock $vehiclesMenuBlock,
        VehiclesNamesMap $vehiclesNamesMap
    )
    {
        $this->I = $I;
        $this->vehiclesMenuBlock = $vehiclesMenuBlock;
        $this->vehiclesNamesMap = $vehiclesNamesMap;
    }

    public function shouldBeDisplayed() : VehiclesMenuSteps
    {
        $this->I->logNotice('Проверяем, что меню Vehicles - отобразилось');

        $this->vehiclesMenuBlock->shouldBeDisplayed();

        return $this;
    }

    public function checkPrices() : VehiclesMenuSteps
    {
        $this->I->logNotice('Проверяем, что цены авто находятся в заданном диапазоне');

        $vehicles = $this
                        ->vehiclesMenuBlock
                        ->getCardsArray()
                        ->shouldBeGreaterThanOrEqual(5)
                        ->getElementsMap('getVehicleName');

        /**
         * @var string $name
         * @var DodgeVehicleCard $card
         */
        foreach ($vehicles as $name => $card)
        {
            if (!$this->vehiclesNamesMap->hasValue($name))
            {
                continue;
            }

            if (!$card->hasPrice())
            {
                $this->I->fail("Для авто: $name - не отображается цена");
            }

            $alias = $this->vehiclesNamesMap->getAlias($name);

            $price = $card->_getPrice();

            switch ($alias)
            {
                case 'Alias: Charger':
                    $this->I->assertGreaterThan(20000, $price); break;

                case 'Alias: Challenger':
                    $this->I->assertGreaterThan(26000, $price); break;

                case 'Alias: Durango':
                    $this->I->assertGreaterThan(28000, $price); break;

                case 'Alias: Journey':
                    $this->I->assertGreaterThan(18000, $price); break;

                case 'Alias: Grand Caravan':
                    $this->I->assertGreaterThan(22000, $price); break;
            }
        }

        return $this;
    }

    public function selectVehicle(string $alias) : VehiclePageSteps
    {
        $name = $this->vehiclesNamesMap->getValue($alias);

        $this->I->logNotice('Выбираем авто: ' . $name);

        $vehicles = $this
                        ->vehiclesMenuBlock
                        ->getCardsArray()
                        ->shouldBeGreaterThanOrEqual(5)
                        ->getElementsMap('getVehicleName');

        if (!isset($vehicles[$name]))
        {
            throw new UsageException('Среди отображаемых авто: ' . implode(', ', array_keys($vehicles)) . ' - нет авто с названием: ' . $name);
        }

        /** @var DodgeVehicleCard $vehicle */
        $vehicle = $vehicles[$name];
        $vehicle->click();

        return DodgeSteps::$vehiclePageSteps->shouldBeDisplayed();
    }
}
