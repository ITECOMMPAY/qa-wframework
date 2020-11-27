<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 16:06
 */

namespace Common\Module\WFramework\WebObjects\Base\WElement;

use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Helpers\EmptyComposite;
use Common\Module\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Common\Module\WFramework\WebObjects\Base\WBlock\WBlock;
use Common\Module\WFramework\WebObjects\Base\WPageObject;
use Common\Module\WFramework\WLocator\WLocator;
use function strpos;

/**
 * WElement - это одна из разновидностей PageObject'а.
 *
 * Если наследники WBlock описывают конкретный блок страницы, то наследники WElement описывают общие части сайта, которые
 * могут повсеместно встречаться на его страницах и иметь одно и то же поведение.
 * Примером этого пункта являются: кнопки, поля ввода, выпадающие списки, таблицы.
 *
 * Другим отличительным признаком WElement является инкапсуляция своей логики.
 * Наследники WBlock не содержат внутри себя сложной логики - вся их логика лежит снаружи в тестах, а WBlock дают тестам
 * прямой доступ к своим элементам.
 * WElement, напротив, напичканы сложной логикой, но прячут её за красивым интерфейсом. Тесты взаимодействуют с WElement
 * через интерфейс и никогда не лезут внутрь.
 * Примером этого пункта является: виджет часового пояса. Вместо того чтобы заставлять тестировщиков открывать выпадающий
 * список часовых поясов, выбирать в нём первую букву имени местоположения и далее выбирать конкретный город из
 * загрузившегося списка, виджет часового пояса просто предоставляет метод selectLocation(), который делает всё это сам.
 *
 * WElement получают своё конкретное имя и локатор - извне, обычно при объявлении внутри WBlock, в то время как имя
 * и локатор WBlock объявляется внутри самого WBlock.
 *
 * Для удобства наследники WElement в фреймворке называются веб-элементами.
 *
 * @package Common\Module\WFramework\WebObjects\Base\WElement
 */
abstract class WElement extends WPageObject
{
    /**
     * WElement получают своё конкретное имя и локатор при объявлении внутри WBlock.
     *
     * Но помимо конкретного имени, WElement имеют общее имя для данной разновидности WElement.
     * Например: кнопка, поле ввода, календарь.
     *
     * @var string
     */
    protected $typeName = '';

    /**
     * Данный метод должен возвращать общее имя для данной разновидности веб-элементов.
     *
     * Например: кнопка, поле ввода, календарь.
     *
     * Он нужен потому что из одного имени класса не всегда понятно - что это за веб-элемент и зачем он нужен.
     *
     * @return string
     */
    abstract protected function initTypeName() : string;

    /**
     * Создаёт данный веб-элемент из локатора.
     *
     * Например:
     *
     *      $this->emailField = WTextBox::fromLocator('Email',    WLocator::xpath(".//input[@name='email']"));
     *
     * @param string $name - понятное для человека описание данного веб-элемента. Исходя из него тестировщик должен
     *                       однозначно понять - о каком элементе страницы идёт речь.
     *                       Например: 'поле ввода Email', 'кнопка Логина'. Слова 'поле ввода' или 'кнопка' - лучше
     *                       опустить т.к. это и так понятно из имени класса. Имя должно быть уникальным в рамках WBlock.
     * @param WLocator $locator - локатор веб-элемента
     * @param bool $relative - определён ли локатор данного веб-элемента относительно локатора родительского элемента
     *                         (PageObject'а или другого веб-элемента). Необязательный параметр. По-умолчанию в True.
     * @return static
     */
    public static function fromLocator(string $name, WLocator $locator, bool $relative = true)
    {
        return new static(WFrom::locator($name, $locator, $relative));
    }

    /**
     * Создаёт данный веб-элемент из локатора XPath.
     *
     *
     *
     * Например:
     *
     *      $this->emailField = WTextBox::fromXpath('Email', ".//input[@name='email']");
     *
     *
     * @param string $name - понятное для человека описание данного веб-элемента. Исходя из него тестировщик должен
     *                       однозначно понять - о каком элементе страницы идёт речь.
     *                       Например: 'поле ввода Email', 'кнопка Логина'. Слова 'поле ввода' или 'кнопка' - лучше
     *                       опустить т.к. это и так понятно из имени класса. Имя должно быть уникальным в рамках WBlock.
     * @param string $XPath - XPath локатор веб-элемента. Согласно веб-стандарту относительные XPath должны начинаться
     *                        с точки. Если XPath не начинается с точки то он ищется относительно корня страницы.
     * @return static
     */
    public static function fromXpath(string $name, string $XPath)
    {
        return static::fromLocator($name, WLocator::xpath($XPath), strpos($XPath, '.') === 0);
    }

    /**
     * Создаёт данный веб-элемент из FacadeWebElement.
     *
     * @param string $name - понятное для человека описание данного веб-элемента. Имя должно быть уникальным в рамках WBlock.
     * @param FacadeWebElement $facadeWebElement
     * @return mixed
     */
    public static function fromFacadeWebElement(string $name, FacadeWebElement $facadeWebElement)
    {
        return new static (WFrom::facadeWebElement($name, $facadeWebElement));
    }

    /**
     * Создаёт данный веб-элемент из другого веб-элемента.
     *
     * Если возникла вдруг необходимость нажать по надписи или ввести текст в кнопку =\
     *
     * @param WElement $element
     * @return mixed
     */
    public static function fromAnotherWElement(WElement $element)
    {
        return new static (WFrom::anotherWElement($element));
    }

    /**
     * Конструктор веб-элемента.
     *
     * Создаёт новый экземпляр данного класса из экземпляра абстрактной фабрики WFrom.
     * Экземпляр абстрактной фабрики должен иметь методы getName(), getLocator(), getRelative() и getFacadeWebElement().
     *
     * Рекомендуется использовать статические методы fromLocator(), fromFacadeWebElement() и fromAnotherWElement()
     * вместо прямого вызова конструктора.
     *
     * Пример:
     *
     *      $this->emailField = new WTextBox(new WFromLocator('Email', WLocator::xpath(".//input[@name='email']")));
     *
     * , рекомендуемый аналог:
     *
     *      $this->emailField = WTextBox::fromXpath('Email', ".//input[@name='email']");
     *
     * @param WFrom $importer
     */
    public function __construct(WFrom $importer)
    {
        parent::__construct();

        $this->typeName = $this->initTypeName();
        $this->name = $importer->getName();
        $this->locator = $importer->getLocator();
        $this->relative = $importer->getRelative();
        $this->facadeWebElement = $importer->getFacadeWebElement();
        $this->setParent($importer->getParent());
    }

    /**
     * Возвращает общее имя для данной разновидности веб-элементов, заданное методом initTypeName()
     *
     * @return string
     */
    public function getTypeName() : string
    {
        return $this->typeName;
    }

    public function __toString() : string
    {
        if ($this->relative && !$this->getParent() instanceof EmptyComposite)
        {
            return $this->getParent() . ' / ' . $this->getClassShort() . ' (' . $this->getName() . ')';
        }

        return '/ ' . $this->getClassShort() . ' (' . $this->getName() . ')';
    }

    /**
     * Возвращает WBlock, на котором зарегистрирован данный веб-элемент.
     *
     * @return WBlock
     * @throws \Exception
     */
    protected function getWBlock() : WBlock
    {
        $parent = $this->getParent();

        while (!$parent instanceof WBlock)
        {
            if ($parent instanceof EmptyComposite)
            {
                throw new UsageException($this . ' -> должен располагаться на WBlock.');
            }

            $parent = $parent->getParent();
        }

        return $parent;
    }

    /**
     * PageObject должен иметь заданный текст.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь заданный текст в любом регистре,
     * при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но строка: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout) - валит тест.
     *
     * @param string $text
     * @return WElement
     * @throws UsageException
     */
    public function shouldHaveText(string $text)
    {
        return $this->should(Cond::text($text), "должен иметь текст: $text");
    }

    /**
     * PageObject имеет заданный текст?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь заданный текст в любом регистре,
     * при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но строка: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param string $text
     * @return bool
     */
    public function isHavingText(string $text) : bool
    {
        return $this->is(Cond::text($text), "имеет текст: '$text'?");
    }

    /**
     * PageObject должен иметь заданное значение атрибута value.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value состоящее из заданного текста
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но value: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout) - валит тест.
     *
     * @param string $value
     * @return WElement
     * @throws UsageException
     */
    public function shouldHaveValue(string $value)
    {
        return $this->should(Cond::value($value), "должен иметь значение: $value");
    }

    /**
     * PageObject имеет заданное значение атрибута value?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value состоящее из заданного текста
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но value: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param string $value
     * @return bool
     */
    public function isHavingValue(string $value) : bool
    {
        return $this->is(Cond::value($value), "имеет значение: '$value'?");
    }

    /**
     * PageObject должен содержать заданное значение атрибута value.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value содержащее заданный текст
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout) - валит тест.
     *
     * @param string $value
     * @return WElement
     * @throws UsageException
     */
    public function shouldContainValue(string $value)
    {
        return $this->should(Cond::valueThatContains($value), "должен содержать значение: $value");
    }

    /**
     * PageObject содержит заданное значение атрибута value?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value содержащее заданный текст
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param string $value
     * @return bool
     */
    public function isContainingValue(string $value) : bool
    {
        return $this->is(Cond::valueThatContains($value), "содержит значение: '$value'?");
    }

    public function getValue() : string
    {
        WLogger::logInfo($this . " -> получаем значение");

        $result = $this
                        ->returnSeleniumElement()
                        ->get()
                        ->value()
                        ;

        WLogger::logInfo($this . " -> имеет значение: '$result'");

        return $result;
    }
}
