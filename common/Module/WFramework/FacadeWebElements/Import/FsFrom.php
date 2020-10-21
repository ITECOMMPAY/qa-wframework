<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:37
 */

namespace Common\Module\WFramework\FacadeWebElements\Import;


use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\ProxyWebElements\ProxyWebElements;
use Common\Module\WFramework\WLocator\WLocator;
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
