<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 12:35
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Facebook\WebDriver\WebDriverKeys;

/**
 * Категория методов FacadeWebElement, которая содержит набор методов для эмуляции действий с клавиатуры для данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class KeyboardGroup extends OperationsGroup
{
    /**
     * Посылает символы элементу.
     *
     * Для эмуляции нажатия комбинации специальных клавиш вместе с обычными - следует использовать массив.
     * Специальные клавиши содержаться в классе WebDriverKeys.
     *
     * Например:
     *
     *      $facadeWebElement
     *                     ->keyboard()
     *                     ->pressKeys([WebDriverKeys::CONTROL, WebDriverKeys::END])
     *                     ;
     *
     * @param string|array|int|float $keys - символы, которые нужно послать элементу
     * @return KeyboardGroup
     */
    public function pressKeys($keys) : KeyboardGroup
    {
        WLogger::logDebug('Посылаем элементу символы: ' . $keys);

        $this
            ->getProxyWebElement()
            ->sendKeys($keys)
            ;

        return $this;
    }

    /**
     * Посылает Enter данному элементу.
     *
     * @return KeyboardGroup
     */
    public function pressEnter() : KeyboardGroup
    {
        WLogger::logDebug('Посылаем элементу Enter');

        $this
            ->getProxyWebElement()
            ->sendKeys(WebDriverKeys::ENTER)
            ;

        return $this;
    }

    /**
     * Посылает Esc данному элементу.
     *
     * @return KeyboardGroup
     */
    public function pressEsc() : KeyboardGroup
    {
        WLogger::logDebug('Посылаем элементу Esc');

        $this
            ->getProxyWebElement()
            ->sendKeys(WebDriverKeys::ESCAPE)
            ;

        return $this;
    }

    /**
     * Посылает Tab данному элементу.
     *
     * @return KeyboardGroup
     */
    public function pressTab() : KeyboardGroup
    {
        WLogger::logDebug('Посылаем элементу Tab');

        $this
            ->getProxyWebElement()
            ->sendKeys(WebDriverKeys::TAB)
            ;

        return $this;
    }
}
