<?php


namespace dodge\Helper\TestSteps;


use Common\Module\WFramework\StepsGroup\StepsGroup;

class DodgeSteps extends StepsGroup
{
    /** @var FrontPageSteps */
    public static $frontPageSteps;

    /** @var VehiclesMenuSteps */
    public static $vehiclesMenuSteps;

    public function _inject(
        FrontPageSteps $frontPageSteps,
        VehiclesMenuSteps $vehiclesMenuSteps
    )
    {
        static::$frontPageSteps = $frontPageSteps;
        static::$vehiclesMenuSteps = $vehiclesMenuSteps;
    }
}
