<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:38
 */

namespace Codeception\Lib\WFramework\FacadeWebElements\Import;


use Codeception\Lib\WFramework\ProxyWebElements\ProxyWebElements;

class FsFromProxyWebElements extends FsFrom
{
    public function __construct(ProxyWebElements $proxyWebElements)
    {
        $this->proxyWebElements = $proxyWebElements;
    }
}
