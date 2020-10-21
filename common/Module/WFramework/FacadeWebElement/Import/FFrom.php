<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.03.19
 * Time: 15:47
 */

namespace Common\Module\WFramework\FacadeWebElement\Import;


use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;
use Common\Module\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;


/**
 * Абстрактная фабрика для использования в конструкторе FacadeWebElement.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Import
 */
abstract class FFrom
{
    protected $proxyWebElement = null;

    /**
     * Возвращает настроенный ProxyWebElement, который будет обёрнут в FacadeWebElement.
     *
     * @return ProxyWebElement
     */
    public function getProxyWebElement() : ProxyWebElement
    {
        return $this->proxyWebElement;
    }

    /**
     * Помогает создать FacadeWebElement из локатора.
     *
     * @param WLocator $locator - локатор Селениума
     * @param RemoteWebDriver $webDriver - экземпляр Селениума
     * @param FacadeWebElement|null $parentElement - родительский элемент, относительно локатора которого будет
     *                                               производиться поиск данного элемента. Необязательный параметр.
     * @return FFrom
     */
    public static function locator(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null) : FFrom
    {
        return new FFromLocator($locator, $webDriver, $parentElement);
    }

    /**
     * Помогает создать FacadeWebElement из готового ProxyWebElement.
     *
     * @param ProxyWebElement $proxyWebElement - настроенный ProxyWebElement.
     * @return FFrom
     */
    public static function proxyWebElement(ProxyWebElement $proxyWebElement) : FFrom
    {
        return new FFromProxyWebElement($proxyWebElement);
    }
}
