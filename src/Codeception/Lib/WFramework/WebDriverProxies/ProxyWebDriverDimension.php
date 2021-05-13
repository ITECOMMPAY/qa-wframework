<?php


namespace Codeception\Lib\WFramework\WebDriverProxies;


use Facebook\WebDriver\WebDriverDimension;

class ProxyWebDriverDimension extends WebDriverDimension
{
    public static function fromWebDriverDimension(WebDriverDimension $dimension) : ProxyWebDriverDimension
    {
        return new ProxyWebDriverDimension($dimension->getWidth(), $dimension->getHeight());
    }

    public function __toString()
    {
        return "{$this->getWidth()}x{$this->getHeight()}";
    }
}