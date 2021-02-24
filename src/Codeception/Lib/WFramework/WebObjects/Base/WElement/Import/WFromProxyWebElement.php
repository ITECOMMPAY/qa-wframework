<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.03.19
 * Time: 14:18
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WElement\Import;


use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class WFromProxyWebElement extends WFrom
{
    //TODO как WBlock передавать?

    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $name, ProxyWebElement $proxyWebElement, WPageObject $parent)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->name = $name;
        $this->locator = $proxyWebElement->getLocator();
        $this->proxyWebElement = $proxyWebElement;
        $this->relative = $proxyWebElement->getParentElement() !== null;
        $this->parentElement = $parent;
    }
}
