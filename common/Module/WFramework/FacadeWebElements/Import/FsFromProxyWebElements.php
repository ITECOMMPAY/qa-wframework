<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 14:38
 */

namespace Common\Module\WFramework\FacadeWebElements\Import;


use Common\Module\WFramework\ProxyWebElements\ProxyWebElements;

class FsFromProxyWebElements extends FsFrom
{
    public function __construct(ProxyWebElements $proxyWebElements)
    {
        $this->proxyWebElements = $proxyWebElements;
    }
}
