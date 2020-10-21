<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 12:58
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;


/**
 * Категория методов FacadeWebElement, которая содержит набор методов для проверки выполнения условий для данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class IsGroup extends OperationsGroup
{
    /**
     * Проверяет, что заданные условия выполняются для данного элемента.
     *
     * @param Cond ...$conditions - условия
     * @return bool - True, если заданные условия выполняются для данного элемента
     */
    public function is(Cond ...$conditions) : bool
    {
        WLogger::logDebug('Проверяем, что для элемента выполняются заданные условия');

        foreach ($conditions as $cond)
        {
            if (!$this->checkCondition($cond))
            {
                return False;
            }
        }

        return True;
    }

    /**
     * Проверяет, что заданные условия НЕ выполняются для данного элемента.
     *
     * @param Cond ...$conditions - условия
     * @return bool - True, если заданные условия НЕ выполняются для данного элемента
     */
    public function isNot(Cond ...$conditions) : bool
    {
        WLogger::logDebug('Проверяем, что для элемента НЕ выполняются заданные условия');

        foreach ($conditions as $cond)
        {
            if (!$this->checkCondition($cond, true))
            {
                return False;
            }
        }

        return True;
    }

    /**
     * Синоним для метода is().
     *
     * Проверяет, что заданные условия выполняются для данного элемента.
     *
     * @param Cond ...$conditions - условия
     * @return bool - True, если заданные условия выполняются для данного элемента
     */
    public function has(Cond ...$conditions) : bool
    {
        return $this->is(...$conditions);
    }

    /**
     * Синоним для метода isNot().
     *
     * Проверяет, что заданные условия НЕ выполняются для данного элемента.
     *
     * @param Cond ...$conditions - условия
     * @return bool - True, если заданные условия НЕ выполняются для данного элемента
     */
    public function doesNotHave(Cond ...$conditions) : bool
    {
        return $this->isNot(...$conditions);
    }

    /**
     * Проверяет, что элемент существует (т.е. находится в коде страницы).
     *
     * @return bool - True, если элемент существует
     */
    public function exists() : bool
    {
        WLogger::logDebug('Проверяем, что элемент существует');

        return $this->checkCondition(Cond::exist());
    }

    /**
     * Проверяет, что элемент отображается на странице (не обязательно в рамках экрана).
     *
     * @return bool - True, если элемент отображается.
     */
    public function displayed() : bool
    {
        WLogger::logDebug('Проверяем, что элемент отображается');

        return $this->checkCondition(Cond::visible());
    }

    /**
     * Проверяет, что элемент отображается на экране.
     *
     * @return bool - True, если элемент отображается на экране.
     */
    public function inView() : bool
    {
        return $this->checkCondition(Cond::inView());
    }

    /**
     * Проверяет, что элемент доступен для взаимодействия.
     *
     * @return bool - True, если элемент доступен.
     */
    public function enabled() : bool
    {
        WLogger::logDebug('Проверяем, что элемент доступен');

        return $this->checkCondition(Cond::enabled());
    }

    /**
     * Проверяет, что картинка (элемент с тэгом 'img') - загрузилась.
     *
     * @return bool - True, если картинка загрузилась.
     */
    public function image() : bool
    {
        WLogger::logDebug('Проверяем, что картинка загрузилась');

        $element = $this->getProxyWebElement();

        if ($element->getTagName() !== 'img')
        {
            return False;
        }

        $script = "return arguments[0].complete && typeof arguments[0].naturalWidth != 'undefined' && arguments[0].naturalWidth > 0;";

        return $element->executeScriptOnThis($script);
    }

    protected function checkCondition(Cond $cond, bool $invert = false) : bool
    {
        $condition = $invert ? Cond::not($cond) : $cond;

        try
        {
            $passed = $condition->check($this->facadeWebElement);
        }
        catch (NoSuchElementException $e)
        {
            $passed = False;
        }
        catch (StaleElementReferenceException $e)
        {
            $passed = False;
        }

        return $passed;
    }
}
