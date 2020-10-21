<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.03.19
 * Time: 15:52
 */

namespace Common\Module\WFramework\FacadeWebElement\Import;


use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;


/**
 * Конкретная фабрика для использования в конструкторе FacadeWebElement.
 * Помогает создать FacadeWebElement из готового ProxyWebElement.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Import
 */
class FFromProxyWebElement extends FFrom
{
    /**
     * Конструктор конкретной фабрики.
     *
     * @param ProxyWebElement $proxyWebElement - настроенный ProxyWebElement.
     */
    public function __construct(ProxyWebElement $proxyWebElement)
    {
        $this->proxyWebElement = $proxyWebElement;
    }
}
