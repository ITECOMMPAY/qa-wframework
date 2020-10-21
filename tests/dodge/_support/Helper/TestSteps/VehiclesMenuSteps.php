<?php


namespace dodge\Helper\TestSteps;


use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\StepsGroup\StepsGroup;
use dodge\DodgeTester;
use dodge\Helper\AliasMaps\VehiclesNamesMap;
use dodge\Helper\Blocks\VehiclesMenuBlock;
use dodge\Helper\Elements\DodgeVehicleCard\DodgeVehicleCard;

class VehiclesMenuSteps extends StepsGroup
{
    /** @var DodgeTester */
    protected $I;

    /** @var VehiclesMenuBlock */
    public $vehiclesMenuBlock;

    /** @var VehiclesNamesMap */
    public $vehiclesNamesMap;

    protected function _inject(
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

    public function selectVehicle(string $alias)
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

        return $this;
    }
}
