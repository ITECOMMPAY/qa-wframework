<?php


namespace dodge\Helper\TestSteps;


use Codeception\Lib\WFramework\StepsGroup\StepsGroup;

class DodgeSteps extends StepsGroup
{
    /** @var FrontPageSteps */
    public static $frontPageSteps;

    /** @var VehiclesMenuSteps */
    public static $vehiclesMenuSteps;

    /** @var VehiclePageSteps */
    public static $vehiclePageSteps;

    /** @var SelectModelSteps */
    public static $selectModelSteps;

    /** @var BuildModelSteps */
    public static $buildModelSteps;

    public function _inject(
        FrontPageSteps $frontPageSteps,
        VehiclesMenuSteps $vehiclesMenuSteps,
        VehiclePageSteps $vehiclePageSteps,
        SelectModelSteps $selectModelSteps,
        BuildModelSteps $buildModelSteps
    )
    {
        static::$frontPageSteps = $frontPageSteps;
        static::$vehiclesMenuSteps = $vehiclesMenuSteps;
        static::$vehiclePageSteps = $vehiclePageSteps;
        static::$selectModelSteps = $selectModelSteps;
        static::$buildModelSteps = $buildModelSteps;
    }
}
