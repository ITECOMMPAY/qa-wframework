<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:37
 */

namespace Codeception\Lib\WFramework\FacadeWebElements\Import;


use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;
use Codeception\Lib\WFramework\ProxyWebElements\ProxyWebElements;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class FsFrom
{
    protected $proxyWebElements = null;

    public function getProxyWebElements() : ProxyWebElements
    {
        return $this->proxyWebElements;
    }

    public static function locator(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null) : FsFrom
    {
        return new FsFromLocator($locator, $webDriver, $parentElement);
    }

    public static function proxyWebElements(ProxyWebElements $proxyWebElements) : FsFrom
    {
        return new FsFromProxyWebElements($proxyWebElements);
    }
}
