<?php


namespace Common\Module\WFramework\WebObjects\Base\Interfaces;


interface IResetOnShot extends IVolatile
{
    public function defaultStateSet();

    public function defaultStateUnset();
}
