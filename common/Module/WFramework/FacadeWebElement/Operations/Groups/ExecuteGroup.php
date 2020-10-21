<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 04.03.19
 * Time: 16:49
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\ProxyWebElement\ProxyWebDriverActions;


/**
 * Категория методов FacadeWebElement, которая содержит набор методов для выполнения JS-скриптов с данным элементом.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class ExecuteGroup extends OperationsGroup
{
    /**
     * Выполняет JavaScript в браузере.
     *
     * Для выполнения скрипта с данным элементом в качестве аргумента - используйте метод scriptOnThis().
     *
     * @param string $script - строка, содержащая код на языке JavaScript
     * @param array $arguments - массив аргументов. В запускаемом JavaScript они будут лежать в
     *                           массиве 'arguments': arguments[0], arguments[1] и т.д.
     * @return mixed - если скрипт возвращает значение, то значение скрипта.
     */
    public function script(string $script, array $arguments = [])
    {
        WLogger::logDebug('Выполняем скрипт: ' . $script);

        return $this
                    ->getProxyWebElement()
                    ->executeScript($script, $arguments)
                    ;
    }

    /**
     * Выполняет JavaScript с данным элементом в качестве аргумента (arguments[0]).
     *
     * Пример:
     *
     *     $element->executeScriptOnThis("arguments[0].scrollIntoView(true);"); // - прокручиваем окно к данному элементу
     *
     * @param string $script - строка, содержащая код на языке JavaScript
     * @param array $arguments - массив аргументов. В запускаемом JavaScript они будут лежать в
     *                           массиве 'arguments', и будут начинаться с индекса 1, т.к. по нулевому индексу
     *                           лежит данный элемент: arguments[1], arguments[2] и т.д.
     * @return mixed - если скрипт возвращает значение, то значение скрипта.
     */
    public function scriptOnThis(string $script, array $arguments = [])
    {
        WLogger::logDebug('Выполняем скрипт для элемента: ' . $script);

        return $this
                    ->getProxyWebElement()
                    ->executeScriptOnThis($script, $arguments)
                    ;
    }

    /**
     * Инициализирует выполнение Selenium Actions для данного элемента.
     *
     * Selenium Actions позволяет эмулировать комплексные действия пользователя:
     * https://seleniumhq.github.io/selenium/docs/api/java/org/openqa/selenium/interactions/Actions.html
     *
     * После составления цепочки действий её необходимо отправить на выполнение, с помощью метода perform().
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->execute()
     *                      ->actions()
     *                      ->moveToElement(0, 0)
     *                      ->contextClick()
     *                      ->perform()
     *                      ;
     *
     * @return ProxyWebDriverActions
     */
    public function actions() : ProxyWebDriverActions
    {
        WLogger::logDebug('Выполняем Selenium Actions для элемента');

        return $this
                    ->getProxyWebElement()
                    ->executeActions()
                    ;
    }
}
