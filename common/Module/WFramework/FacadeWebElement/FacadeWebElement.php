<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 04.03.19
 * Time: 14:49
 */

namespace Common\Module\WFramework\FacadeWebElement;


use Common\Module\WFramework\FacadeWebElement\Import\FFrom;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\EditGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\ExecuteGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\FieldGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\FindGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\GetGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\IsGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\KeyboardGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\MouseGroup;
use Common\Module\WFramework\FacadeWebElement\Operations\Groups\WaitGroup;
use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;
use Common\Module\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Class FacadeWebElement
 *
 * Данный класс предоставляет удобный интерфейс к элементу Селениума.
 *
 * Он группирует методы для работы с элементом Селениума в отдельные логические категории и реализует концепцию
 * Fluent interface (https://ru.wikipedia.org/wiki/Fluent_interface) для упрощения взаимодействия с ним.
 *
 * Например, вместо такого кода:
 *
 *     $webElement->executeScript("arguments[0].scrollIntoView(true);", webElement); //скроллим окно к полю ввода
 *     $webElement->click(); //кликаем на нём, чтобы убрать плейсхолдер
 *     usleep(intdiv(TestProperties::getValue('elementTimeout') * 1000000), 8); //ждём пока плейсхолдер исчезнет и поле станет доступным для ввода
 *     $webElement->sendKeys(text); //задаём текст
 *
 * FacadeWebElement позволяет написать следующий код:
 *
 *     $facadeWebElement
 *                      ->mouse()
 *                      ->scrollTo()
 *                      ->clickWithLeftButton()
 *                      ->and()
 *                      ->wait()
 *                      ->forEighthTimeout()
 *                      ->then()
 *                      ->field()
 *                      ->set($text)
 *                      ;
 *
 * Переход из одной категории методов в другую производится с помощью равнозначных методов then() или and().
 *
 * Внутри FacadeWebElement содержится ссылка на обёрнутый в него экземпляр прокси-элемента Селениума (ProxyWebElement).
 * Если методов FacadeWebElement недостаточно для работы - можно обратиться к обёрнутому прокси-элементу с помощью метода
 * returnProxyWebElement().
 *
 * @package Common\Module\WFramework\FacadeWebElement
 */
class FacadeWebElement
{
    protected $proxyWebElement = null;

    /**
     * Создаёт экземпляр FacadeWebElement из класса-локатора.
     *
     * Пример:
     *
     *     $facadeWebElement = FacadeWebElement::fromLocator(WLocator::xpath("//input[@name='email']"), $webDriver);
     *
     * @param WLocator $locator - локатор элемента
     * @param RemoteWebDriver $webDriver - экземпляр Selenium WebDriver
     * @param FacadeWebElement|null $parentElement - родительский FacadeWebElement, относительно локатора которого будет
     *                                               производиться поиск данного элемента. Необязательный параметр.
     * @return FacadeWebElement - новый экземпляр данного класса
     */
    public static function fromLocator(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null)
    {
        return new static(FFrom::locator($locator, $webDriver, $parentElement));
    }

    /**
     * Создаёт экземпляр FacadeWebElement из ProxyWebElement.
     *
     * Пример:
     *
     *     $facadeWebElement = FacadeWebElement::fromProxyWebElement($someProxyWebElement);
     *
     * @param ProxyWebElement $proxyWebElement
     * @return FacadeWebElement - новый экземпляр данного класса
     */
    public static function fromProxyWebElement(ProxyWebElement $proxyWebElement)
    {
        return new static(FFrom::proxyWebElement($proxyWebElement));
    }

    /**
     * Конструктор FacadeWebElement.
     *
     * Создаёт новый экземпляр данного класса из экземпляра абстрактной фабрики FFrom.
     * Экземпляр абстрактной фабрики должен иметь метод getProxyWebElement(), возвращающий ProxyWebElement.
     *
     * Рекомендуется использовать статические методы fromLocator() и fromProxyWebElement() вместо прямого вызова
     * конструктора.
     *
     * Пример:
     *
     *     $facadeWebElement = new FacadeWebElement(new FFromLocator(WLocator::xpath("//input[@name='email']"), $webDriver));
     *
     * , рекомендуемый аналог:
     *
     *     $facadeWebElement = FacadeWebElement::fromLocator(WLocator::xpath("//input[@name='email']"), $webDriver);
     *
     * @param FFrom $importer - экземпляр абстрактной фабрики FFrom.
     */
    public function __construct(FFrom $importer)
    {
        $this->proxyWebElement = $importer->getProxyWebElement();
    }

    /**
     * @return ProxyWebElement - обёрнутый ProxyWebElement
     */
    public function returnProxyWebElement() : ProxyWebElement
    {
        return $this->proxyWebElement;
    }

    /**
     * Возвращает набор методов для правки кода страницы
     *
     * @return EditGroup - набор методов для правки кода страницы.
     */
    public function edit() : EditGroup
    {
        return $this->edit ?? $this->edit = new EditGroup($this);
    }

    /**
     * Возвращает набор методов для выполнения JS-скриптов с данным элементом.
     *
     * Пример:
     *
     *     $focusedElement = $facadeWebElement
     *                                       ->exec()
     *                                       ->script("return document.activeElement;")
     *                                       ;
     *
     * @return ExecuteGroup - набор методов для выполнения JS-скриптов с данным элементом.
     */
    public function exec() : ExecuteGroup
    {
        return $this->exec ?? $this->exec = new ExecuteGroup($this);
    }

    /**
     * Возвращает набор методов для работы с текстом данного элемента.
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->field()
     *                      ->clear()
     *                      ;
     *
     * @return FieldGroup - набор методов для работы с текстом данного элемента.
     */
    public function field() : FieldGroup
    {
        return $this->field ?? $this->field = new FieldGroup($this);
    }

    /**
     * Возвращает набор методов для поиска новых элементов относительно данного элемента.
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->find()
     *                      ->element($locator)
     *                      ;
     *
     * @return FindGroup - набор методов для поиска новых элементов относительно данного элемента.
     */
    public function find() : FindGroup
    {
        return $this->find ?? $this->find = new FindGroup($this);
    }

    /**
     * Возвращает набор методов для получения атрибутов и свойств данного элемента.
     *
     * Пример:
     *
     *     $classes = $facadeWebElement
     *                                ->get()
     *                                ->attribute('class')
     *                                ;
     *
     * @return GetGroup - набор методов для получения атрибутов и свойств данного элемента.
     */
    public function get() : GetGroup
    {
        return $this->get ?? $this->get = new GetGroup($this);
    }

    /**
     * Возвращает набор методов для проверки выполнения условий для данного элемента.
     *
     * Пример:
     *
     *     $exist = $facadeWebElement
     *                              ->checkIt()
     *                              ->exists()
     *                              ;
     *
     * @return IsGroup - набор методов для проверки выполнения условий для данного элемента.
     */
    public function checkIt() : IsGroup
    {
        return $this->is ?? $this->is = new IsGroup($this);
    }

    /**
     * Возвращает набор методов для эмуляции действий с клавиатуры для данного элемента.
     *
     * Пример:
     *
     *     $facadeWebElement
     *                     ->keyboard()
     *                     ->pressEnter()
     *                     ;
     *
     * @return KeyboardGroup - набор методов для эмуляции действий с клавиатуры для данного элемента.
     */
    public function keyboard() : KeyboardGroup
    {
        return $this->keyboard ?? $this->keyboard = new KeyboardGroup($this);
    }

    /**
     * Возвращает набор методов для эмуляции действий мыши для данного элемента.
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->mouse()
     *                      ->scrollTo()
     *                      ->clickWithLeftButton()
     *                      ;
     *
     * @return MouseGroup - набор методов для эмуляции действий мыши для данного элемента.
     */
    public function mouse() : MouseGroup
    {
        return $this->mouse ?? $this->mouse = new MouseGroup($this);
    }

    /**
     * Возвращает набор методов для ожидания выполнения условий для данного элемента.
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->wait()
     *                      ->until(Cond::exist())
     *                      ;
     *
     * @return WaitGroup - набор методов для ожидания выполнения условий для данного элемента.
     */
    public function wait() : WaitGroup
    {
        return $this->wait ?? $this->wait = new WaitGroup($this);
    }
}
