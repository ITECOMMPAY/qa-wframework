<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:38
 */

namespace Common\Module\WFramework\FacadeWebElements\Import;


use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\ProxyWebElements\ProxyWebElements;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class FsFromLocator extends FsFrom
{
    public function __construct(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null)
    {
        $timeout = (int) TestProperties::getValue('collectionTimeout');

        if ($parentElement === null)
        {
            $this->proxyWebElements = new ProxyWebElements($locator, $webDriver, $timeout);
        }
        else
        {
            $this->proxyWebElements = new ProxyWebElements($locator, $webDriver, $timeout, $parentElement->returnProxyWebElement());
        }
    }
}
