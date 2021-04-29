<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 16:14
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;


use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElements;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WsFromProxyWebElements extends WsFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $instanceName, ProxyWebElements $proxyWebElements, string $elementClass)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->proxyWebElements = $proxyWebElements;
        $locator = $proxyWebElements->getLocator();
        $relative = $proxyWebElements->getParentElement() !== null;

        /** @var WElement firstElement */
        $this->firstElement = $elementClass::fromLocator($instanceName, $locator, $relative);
    }
}
