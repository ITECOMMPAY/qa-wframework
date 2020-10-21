<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 16:14
 */

namespace Common\Module\WFramework\WebObjects\Base\WElements\Import;

use Common\Module\WFramework\WebObjects\Base\WElement\WElement;

class WsFromFirstElement extends WsFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(WElement $webElement)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->instanceName = $webElement->getName();
        $this->locator = $webElement->getLocator();
        $this->relative = $webElement->isRelative();
        $this->elementClass = get_class($webElement);

        $this->facadeWebElements = null;
    }
}
