<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 04.03.19
 * Time: 17:37
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Import\FFrom;
use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WLocator\WLocator;


/**
 * Категория методов FacadeWebElement, которая содержит для поиска новых элементов относительно данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class FindGroup extends OperationsGroup
{
    /**
     * Ищет новый элемент относительно текущего.
     *
     * @param WLocator $by - локатор относительного элемента. Не забывайте, что в случае XPath необходимо чтобы локатор
     *                       относительного элемента начинался с точки.
     * @return FacadeWebElement
     */
    public function element(WLocator $by) : FacadeWebElement
    {
        WLogger::logDebug('Ищем относительный элемент: ' . $by->getValue());

        return new FacadeWebElement(FFrom::proxyWebElement(
            $this
                ->getProxyWebElement()
                ->findElement($by)
        ));
    }

    public function elements(WLocator $by) : FacadeWebElements
    {
        WLogger::logDebug('Ищем относительные элементы: ' . $by->getValue());

        $proxyWebElements = $this
                                ->getProxyWebElement()
                                ->findProxyWebElements($by)
                                ;

        return FacadeWebElements::fromProxyWebElements($proxyWebElements);
    }
}
