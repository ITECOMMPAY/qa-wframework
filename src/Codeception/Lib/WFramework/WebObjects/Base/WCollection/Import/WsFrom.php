<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 15:50
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElements;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class WsFrom
{
    /** @var string */
    protected $instanceName = '';

    /** @var WLocator */
    protected $locator;

    protected $relative = false;

    /** @var string */
    protected $elementClass = '';

    /** @var ProxyWebElements|null  */
    protected $proxyWebElements = null;

    /** @var RemoteWebDriver|null  */
    protected $webDriver = null;

    /** @var WPageObject|null  */
    protected $parentElement = null;


    public function getProxyWebElements()
    {
        return $this->proxyWebElements;
    }

    public function getInstanceName()
    {
        return $this->instanceName;
    }

    public function getLocator()
    {
        return $this->locator;
    }

    public function getRelative()
    {
        return $this->relative;
    }

    public function getElementClass()
    {
        return $this->elementClass;
    }

    public function getParent()
    {
        if ($this->parentElement === null)
        {
            $this->parentElement = EmptyComposite::get();
        }

        return $this->parentElement;
    }

    public function __construct()
    {
        throw new UsageException(
            PHP_EOL . 'Наследника WCollection нельзя напрямую использовать в степах (в т.ч. прописывать в методе _inject) 
                                - он должен располагаться на каком-нибудь WBlock, и именно WBlock должен быть прописан в степах.');
    }

    public static function proxyWebElements(string $name, ProxyWebElements $proxyWebElements, string $elementClass, WPageObject $parent) : WsFrom
    {
        return new WsFromProxyWebElements($name, $proxyWebElements, $elementClass, $parent);
    }

    public static function firstElement(WElement $webElement) : WsFrom
    {
        return new WsFromFirstElement($webElement);
    }
}
