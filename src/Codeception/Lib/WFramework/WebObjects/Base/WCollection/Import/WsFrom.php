<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 15:50
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElements;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;


abstract class WsFrom
{
    /** @var ProxyWebElements|null  */
    protected $proxyWebElements = null;

    /** @var WElement|null */
    protected $firstElement = null;


    public function getProxyWebElements()
    {
        return $this->proxyWebElements;
    }

    public function getFirstElement()
    {
        return $this->firstElement;
    }

    public function __construct()
    {
        throw new UsageException(
            PHP_EOL . 'Наследника WCollection нельзя напрямую использовать в степах (в т.ч. прописывать в методе _inject) 
                                - он должен располагаться на каком-нибудь WBlock, и именно WBlock должен быть прописан в степах.');
    }

    public static function proxyWebElements(string $name, ProxyWebElements $proxyWebElements, string $elementClass) : WsFrom
    {
        return new WsFromProxyWebElements($name, $proxyWebElements, $elementClass);
    }

    public static function firstElement(WElement $webElement) : WsFrom
    {
        return new WsFromFirstElement($webElement);
    }
}
