<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:38
 */

namespace Codeception\Lib\WFramework\FacadeWebElements\Import;


use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;
use Codeception\Lib\WFramework\ProxyWebElements\ProxyWebElements;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WLocator\WLocator;
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
