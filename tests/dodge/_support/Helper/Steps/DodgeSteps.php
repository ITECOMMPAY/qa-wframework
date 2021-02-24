<?php


namespace dodge\Helper\Steps;


use Codeception\Lib\WFramework\Steps\StepsGroup;
use dodge\Helper\Steps\BuildModelSteps;
use dodge\Helper\Steps\FrontPageSteps;
use dodge\Helper\Steps\SelectModelSteps;
use dodge\Helper\Steps\VehiclePageSteps;
use dodge\Helper\Steps\VehiclesMenuSteps;

class DodgeSteps extends StepsGroup
{
    /** @var BuildModelSteps */
    public static $buildModelSteps;
    
    /** @var FrontPageSteps */
    public static $frontPageSteps;
    
    /** @var SelectModelSteps */
    public static $selectModelSteps;
    
    /** @var VehiclePageSteps */
    public static $vehiclePageSteps;
    
    /** @var VehiclesMenuSteps */
    public static $vehiclesMenuSteps;
    

    public function __construct(
        BuildModelSteps $buildModelSteps,
        FrontPageSteps $frontPageSteps,
        SelectModelSteps $selectModelSteps,
        VehiclePageSteps $vehiclePageSteps,
        VehiclesMenuSteps $vehiclesMenuSteps
    )
    {
        static::$buildModelSteps = $buildModelSteps;
        static::$frontPageSteps = $frontPageSteps;
        static::$selectModelSteps = $selectModelSteps;
        static::$vehiclePageSteps = $vehiclePageSteps;
        static::$vehiclesMenuSteps = $vehiclesMenuSteps;
    }
}