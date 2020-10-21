<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.03.19
 * Time: 15:49
 */

namespace Common\Module\WFramework\FacadeWebElement\Import;


use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Конкретная фабрика для использования в конструкторе FacadeWebElement.
 * Помогает создать FacadeWebElement из локатора.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Import
 */
class FFromLocator extends FFrom
{
    /**
     * Конструктор конкретной фабрики.
     *
     * @param WLocator $locator - локатор Селениума
     * @param RemoteWebDriver $webDriver - экземпляр Селениума
     * @param FacadeWebElement|null $parentElement - родительский элемент, относительно локатора которого будет
     *                                               производиться поиск данного элемента. Необязательный параметр.
     */
    public function __construct(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null)
    {
        if ($parentElement === null)
        {
            $this->proxyWebElement = new ProxyWebElement($locator, $webDriver, (int) TestProperties::getValue('elementTimeout'));
        }
        else
        {
            $this->proxyWebElement = new ProxyWebElement($locator, $webDriver, (int) TestProperties::getValue('elementTimeout'), $parentElement->returnProxyWebElement());
        }
    }
}
