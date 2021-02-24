<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 16:14
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;


use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElements;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class WsFromProxyWebElements extends WsFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $instanceName, ProxyWebElements $proxyWebElements, string $elementClass, WPageObject $parent)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->instanceName = $instanceName;
        $this->locator = $proxyWebElements->getLocator();
        $this->relative = $proxyWebElements->getParentElement() !== null;
        $this->elementClass = $elementClass;
        $this->parentElement = $parent;

        $this->proxyWebElements = $proxyWebElements;
    }
}
