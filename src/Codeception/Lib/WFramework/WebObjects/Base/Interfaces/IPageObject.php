<?php


namespace Codeception\Lib\WFramework\WebObjects\Base\Interfaces;

use Codeception\Actor;
use Codeception\Lib\WFramework\Actor\ImaginaryActor;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\Composite;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Ds\Map;
use Ds\Sequence;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Interface IPageObject
 *
 * Описывает базовый интерфейс, который реализуют все PageObject'ы и объекты который косят под них (WCollection).
 *
 * По сути, у PageObject'а есть две группы методов:
 * - методы которые начинаются с is - проверяют, что для PageObject'а выполняется заданное условие и возвращают true или
 *   false;
 * - методы которые начинаются с should - ожидают некоторое время, пока для PageObject'а не начнёт выполняться заданное
 *   условие, а если условие так и не выполнилось - валят тест.
 * - методы которые начинаются с finally - ожидают некоторое время, пока для PageObject'а не начнёт выполняться заданное
 *   условие, а если условие так и не выполнилось - возвращают false.
 *
 * @package Common\Module\WFramework\WebObjects\Base\Interfaces
 */
interface IPageObject
{
    /**
     * С помощью этого метода можно обратиться к методам главного актора Codeception
     *
     * @return Actor|ImaginaryActor
     */
    public function returnCodeceptionActor();

    /**
     * С помощью этого метода можно обратиться к методам Сервера Селениума
     */
    public function returnSeleniumServer() : RemoteWebDriver;

    /**
     * Возвращает полное имя своего класса
     *
     * @return string
     */
    public function getClass() : string;

    /**
     * Возвращает короткое имя своего класса
     *
     * @return string
     */
    public function getClassShort() : string;

    /**
     * Возвращает своё имя
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Возвращает своего родителя
     *
     * Если родителя нет, то возвращает EmptyComposite
     *
     * @return EmptyComposite|IPageObject
     */
    public function getParent();

    /**
     * Возвращает ассоциативный массив детей
     *
     * Массив имеет вид: 'имя Composite' => Composite
     *
     * @return IPageObject[]|Map
     */
    public function getChildren() : Map;

    public function __toString() : string;

    /**
     * Возвращает локатор данного PageObject'а
     *
     * @return WLocator
     */
    public function getLocator() : WLocator;

    /**
     * Определён ли локатор данного PageObject'а относительно его родителя (другого PageObject'а)?
     *
     * @return bool
     */
    public function isRelative() : bool;

    /**
     * Возвращает полный XPath-локатор.
     *
     * @return string
     */
    public function getFullXPath() : string;

    /**
     * Возвращает детей узла в следующем порядке, включая этот узел (1 - этот узел):
     * ```
     *         (1)
     *         / \
     *        /   \
     *       /     \
     *      /       \
     *      2       3
     *     / \     / \
     *    /   \   /   \
     *    4   5   6   7
     * ```
     * @return \Generator
     */
    public function traverseBreadthFirst() : \Generator;

    /**
     * Возвращает детей узла в следующем порядке, включая этот узел (1 - этот узел):
     * ```
     *         (1)
     *         / \
     *        /   \
     *       /     \
     *      /       \
     *      2       5
     *     / \     / \
     *    /   \   /   \
     *    3   4   6   7
     * ```
     *
     * Для PageObject'ов, по умолчанию, следует использовать этот способ т.к. он перебирает элементы страницы сверху-вниз.
     *
     * @return \Generator
     */
    public function traverseDepthFirst() : \Generator;

    /**
     * Возвращает родителей узла в следующем порядке, включая этот узел (1 - этот узел):
     * ```
     *      3
     *      |
     *      |
     *      2
     *     / \
     *    /   \
     *   (1)   X
     * ```
     * @return Sequence
     */
    public function traverseToRoot() : Sequence;

    /**
     * Возвращает родителей узла в следующем порядке, включая этот узел (3 - этот узел):
     * ```
     *      1
     *      |
     *      |
     *      2
     *     / \
     *    /   \
     *   (3)   X
     * ```
     * @return Sequence
     */
    public function traverseFromRoot() : Sequence;

    /**
     * Возвращает первого родителя узла с классом $class
     *
     * @param string $class
     * @return Composite
     * @throws UsageException
     */
    public function getFirstParentWithClass(string $class);

    /**
     * Принимает визитор ().
     *
     * Визитор позволяет динамически навешивать на объект новые операции. Для этого он оборачивает их в отдельный объект.
     *
     * PageObject получает визитора в качестве аргумента данного метода
     * и вызывает у него метод 'accept + короткое имя класса этого PageObject'а', например acceptWElement().
     *
     * В одном визиторе может быть много accept-методов, которые реализуют одну и ту же операцию
     * для разных видов PageObject'ов, например:
     * acceptWElement, acceptWBlock, acceptWCollection, acceptDasButton, acceptBofCalendar и т.д.
     *
     * Если у визитора нет метода под конкретный класс PageObject'а, например acceptBofCalendar, то он попробует
     * вызвать метод для его типа:
     * acceptWElement - для элементов, acceptWBlock - для блоков, acceptWCollection - для коллекций.
     *
     * Большинство стандартных операций и условий для PageObject'ов реализованы через визиторы.
     *
     * @param PageObjectVisitor $visitor
     * @return mixed|void
     */
    public function accept($visitor);

    /**
     * Возвращает связанный с данным типом PageObject'ов таймаут ожидания (используется в should и finally методах)
     *
     * @return int
     */
    public function getTimeout() : int;

    /**
     * PageObject должен существовать.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны существовать.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldExist(bool $deep = true);

    /**
     * PageObject НЕ должен существовать.
     *
     * Для этого элемент на который ссылается локатор PageObject'а НЕ должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны существовать.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldNotExist(bool $deep = true);

    /**
     * PageObject должен отображаться.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен отображаться на странице, но не обязательно
     * в рамках экрана.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны отображаться.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeDisplayed(bool $deep = true);

    /**
     * PageObject НЕ должен отображаться.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен либо отсутствовать в коде страницы, либо
     * быть скрытым.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны отображаться.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeHidden(bool $deep = true);

    /**
     * PageObject должен быть Enabled.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Enabled. Элемент считается Enabled,
     * если он не имеет атрибута disabled или если атрибут disabled стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Enabled.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeEnabled(bool $deep = true);

    /**
     * PageObject должен быть Disabled.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Disabled. Элемент считается Disabled,
     * если атрибут disabled для него стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Disabled.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeDisabled(bool $deep = true);

    /**
     * PageObject должен иметь заданный текст.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь заданный текст в любом регистре,
     * при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но строка: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout для наследников WElement,
     * collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param string $text
     * @return static
     */
    public function shouldHaveText(string $text);

    /**
     * PageObject должен содержать заданный текст.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен содержать заданный текст, начиная с любого
     * места и в любом регистре.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @return static
     */
    public function shouldContainText(string $text);

    /**
     * PageObject должен иметь заданное значение атрибута value.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value состоящее из заданного текста
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - НЕ имеет "заданный текст".
     * Но value: "ЗаДаННый     тЕкСт" - имеет "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param string $value
     * @return static
     */
    public function shouldHaveValue(string $value);

    /**
     * PageObject должен содержать заданное значение атрибута value.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен иметь value содержащее заданный текст
     * в любом регистре, при этом пробельные символы приводятся к одному пробелу.
     *
     * Т.е. value "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param string $value
     * @return static
     */
    public function shouldContainValue(string $value);

    /**
     * PageObject должен быть не только виден, но и находиться внутри рамок окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeInViewport(bool $deep = true);

    /**
     * PageObject должен находиться за рамками окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeOutOfViewport(bool $deep = true);

    /**
     * PageObject должен существовать.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны существовать.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - ввозвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyExist(bool $deep = true) : bool;

    /**
     * PageObject НЕ должен существовать.
     *
     * Для этого элемент на который ссылается локатор PageObject'а НЕ должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны существовать.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyNotExist(bool $deep = true) : bool;

    /**
     * PageObject должен отображаться.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен отображаться на странице, но не обязательно
     * в рамках экрана.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны отображаться.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyDisplayed(bool $deep = true) : bool;

    /**
     * PageObject НЕ должен отображаться.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен либо отсутствовать в коде страницы, либо
     * быть скрытым.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны отображаться.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyHidden(bool $deep = true) : bool;

    /**
     * PageObject должен быть Enabled.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Enabled. Элемент считается Enabled,
     * если он не имеет атрибута disabled или если атрибут disabled стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Enabled.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyEnabled(bool $deep = true) : bool;

    /**
     * PageObject должен быть Disabled.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Disabled. Элемент считается Disabled,
     * если атрибут disabled для него стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Disabled.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyDisabled(bool $deep = true) : bool;

    public function finallyHaveText(string $text) : bool;

    /**
     * PageObject должен содержать заданный текст.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен содержать заданный текст, начиная с любого
     * места и в любом регистре.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @return bool
     */
    public function finallyContainText(string $text) : bool;

    public function finallyHaveValue(string $value) : bool;

    public function finallyContainValue(string $value) : bool;

    /**
     * PageObject должен быть не только виден, но и находиться внутри рамок окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyInViewport(bool $deep = true) : bool;

    /**
     * PageObject должен находиться за рамками окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WCollection) - возвращает false.
     *
     * @param bool $deep
     * @return bool
     */
    public function finallyOutOfViewport(bool $deep = true) : bool;


    /**
     * PageObject существует?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны существовать.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isExist(bool $deep = true) : bool;

    /**
     * PageObject НЕ существует?
     *
     * Для этого элемент на который ссылается локатор PageObject'а НЕ должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны существовать.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isNotExist(bool $deep = true) : bool;

    /**
     * PageObject отображается?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен отображаться на странице, но не обязательно
     * в рамках экрана.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны отображаться.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isDisplayed(bool $deep = true) : bool;

    /**
     * PageObject НЕ отображается?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен либо отсутствовать в коде страницы, либо
     * быть скрытым.
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже НЕ должны отображаться.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isHidden(bool $deep = true) : bool;

    /**
     * PageObject enabled?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Enabled. Элемент считается Enabled,
     * если он не имеет атрибута disabled или если атрибут disabled стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Enabled.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isEnabled(bool $deep = true) : bool;

    /**
     * PageObject disabled?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен быть Disabled. Элемент считается Disabled,
     * если атрибут disabled для него стоит в false.
     *
     * https://html.spec.whatwg.org/#attr-fe-disabled
     *
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны быть Disabled.
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return bool
     */
    public function isDisabled(bool $deep = true) : bool;

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
    public function isHaveText(string $text) : bool;

    /**
     * PageObject содержит заданный текст?
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен содержать заданный текст, начиная с любого
     * места и в любом регистре.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @return bool
     */
    public function isContainText(string $text) : bool;

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
    public function isHaveValue(string $value) : bool;

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
    public function isContainValue(string $value) : bool;

    /**
     * PageObject находится внутри рамок окна браузера?
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return static
     */
    public function isInViewport(bool $deep = true) : bool;

    /**
     * PageObject находится за рамками окна браузера?
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @param bool $deep
     * @return static
     */
    public function isOutOfViewport(bool $deep = true) : bool;
}
