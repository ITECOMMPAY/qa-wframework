<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Steps\StepsGroup;

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

    public function __construct(
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
