<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.03.19
 * Time: 14:18
 */

namespace Common\Module\WFramework\WebObjects\Base\WElement\Import;


use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;

class WFromFacadeWebElement extends WFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $name, FacadeWebElement $facadeWebElement)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->name = $name;
        $this->locator = $facadeWebElement->returnProxyWebElement()->getLocator();
        $this->facadeWebElement = $facadeWebElement;
        $this->relative = $facadeWebElement->returnProxyWebElement()->getParentElement() !== null;
    }
}
