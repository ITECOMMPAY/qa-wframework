<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 28.02.19
 * Time: 17:28
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations;

use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;

/**
 * Class OperationsGroup
 *
 * Данный класс является базовым для всех категорий операций класса FacadeWebElement.
 *
 * Он реализует равнозначные (отличающиеся только названием) методы then() и and(), которые позволяют переходить
 * из одной категории операций в другую.
 *
 * Операции FacadeWebElement оборачивают и расширяют базовые операции элемента Selenium.
 * Желательно чтобы операции FacadeWebElement были независимыми друг-от-друга т.е.
 * не использовали друг-от-друга без особой нужды.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations
 */
abstract class OperationsGroup
{
    protected $facadeWebElement;

    public function __construct(FacadeWebElement $facadeWebElement)
    {
        $this->facadeWebElement = $facadeWebElement;
    }

    /**
     * Равнозначен:
     *
     *     $this->facadeWebElement->returnProxyWebElement();
     *
     * @return ProxyWebElement - ProxyWebElement, который был обёрнут в FacadeWebElement
     */
    protected function getProxyWebElement() : ProxyWebElement
    {
        return $this->facadeWebElement->returnProxyWebElement();
    }

    /**
     * Возвращается из категории в соответствующий FacadeWebElement.
     *
     * То же самое, что then().
     *
     * @return FacadeWebElement
     */
    public function and() : FacadeWebElement
    {
        return $this->facadeWebElement;
    }

    /**
     * Возвращается из категории в соответствующий FacadeWebElement.
     *
     * То же самое, что and().
     *
     * @return FacadeWebElement
     */
    public function then() : FacadeWebElement
    {
        return $this->facadeWebElement;
    }

}
