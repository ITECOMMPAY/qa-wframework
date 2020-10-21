<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 16:14
 */

namespace Common\Module\WFramework\WebObjects\Base\WElements\Import;


use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;

class WsFromFacadeWebElements extends WsFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $instanceName, FacadeWebElements $facadeWebElements, string $elementClass)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->instanceName = $instanceName;
        $this->locator = $facadeWebElements->returnProxyWebElements()->getLocator();
        $this->relative = $facadeWebElements->returnProxyWebElements()->getParentElement() !== null;
        $this->elementClass = $elementClass;

        $this->facadeWebElements = $facadeWebElements;
    }
}
