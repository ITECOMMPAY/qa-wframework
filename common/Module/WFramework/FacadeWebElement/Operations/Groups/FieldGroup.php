<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 13:07
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Facebook\WebDriver\WebDriverKeys;


/**
 * Категория методов FacadeWebElement, которая содержит набор методов для работы с текстом данного элемента.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class FieldGroup extends OperationsGroup
{
    /**
     * Задаёт текст данного элемента (через sendKeys).
     *
     * Если элемент содержал текст - он будет заменён.
     *
     * @param string $value - новый текст для элемента
     * @return FieldGroup
     */
    public function set(string $value) : FieldGroup
    {
        WLogger::logDebug('Задаём значение: ' . $value);

        $this
            ->getProxyWebElement()
            ->clear()
            ->sendKeys($value)
            ;

        return $this;
    }

    /**
     * Очищает текст элемента.
     *
     * @return FieldGroup
     */
    public function clear() : FieldGroup
    {
        WLogger::logDebug('Очищаем поле');

        $this
            ->getProxyWebElement()
            ->sendKeys([WebDriverKeys::CONTROL, 'a'])
            ->sendKeys(WebDriverKeys::BACKSPACE)
            ->clear()
            ;

        return $this;
    }

    /**
     * Добавляет текст в конец имеющегося текста элемента.
     *
     * @param string $value - текст, который следует добавить
     * @return FieldGroup
     */
    public function append(string $value) : FieldGroup
    {
        WLogger::logDebug('Добавляем значение в конец поля: ' . $value);

        $this
            ->getProxyWebElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::END])
            ->sendKeys($value)
            ;

        return $this;
    }

    /**
     * Добавляет текст в начало имеющегося текста элемента.
     *
     * @param string $value - текст, который следует добавить
     * @return FieldGroup
     */
    public function prepend(string $value) : FieldGroup
    {
        WLogger::logDebug('Добавляем значение в начало поля: ' . $value);

        $this
            ->getProxyWebElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::HOME])
            ->sendKeys($value)
            ;

        return $this;
    }

    /**
     * Снимает фокус с элемента
     *
     * @return FieldGroup
     */
    public function clearFocus() : FieldGroup
    {
        WLogger::logDebug('Снимаем фокус с элемента: ');

        $element = $this->getProxyWebElement();

        $element->executeScriptOnThis(static::FIELD_CLEAR_FOCUS);

        return $this;
    }

    /**
     * @param string $filename
     * @return FieldGroup
     */
    public function uploadFile(string $filename): FieldGroup
    {
        $element = $this->getProxyWebElement();

        $element->setFileDetector(new \Facebook\WebDriver\Remote\LocalFileDetector());

        $filePath = codecept_data_dir() . $filename;

        $element->sendKeys(realpath($filePath));

        return $this;
    }

    const FIELD_CLEAR_FOCUS = <<<EOF
arguments[0].blur();
EOF;
}
