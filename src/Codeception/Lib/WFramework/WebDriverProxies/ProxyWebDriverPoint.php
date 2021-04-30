<?php


namespace Codeception\Lib\WFramework\WebDriverProxies;


use Facebook\WebDriver\WebDriverPoint;

class ProxyWebDriverPoint extends WebDriverPoint
{
    public static function fromWebDriverPoint(WebDriverPoint $point) : ProxyWebDriverPoint
    {
        return new ProxyWebDriverPoint($point->getX(), $point->getY());
    }

    public function __toString()
    {
        return "X: {$this->getX()} Y: {$this->getY()}";
    }
}