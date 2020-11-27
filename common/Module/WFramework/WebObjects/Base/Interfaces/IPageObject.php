<?php


namespace Common\Module\WFramework\WebObjects\Base\Interfaces;

use Common\Module\WFramework\ProxyWebElement\ProxyWebElement;
use Common\Module\WFramework\WLocator\WLocator;

/**
 * Interface IPageObject
 *
 * Описывает базовый интерфейс, который реализуют все PageObject'ы.
 *
 * По сути, у PageObject'а есть две группы методов:
 * - методы которые начинаются с is - проверяют, что для PageObject'а выполняется заданное условие и возвращают true или
 *   false;
 * - методы которые начинаются с should - ожидают некоторое время, пока для PageObject'а не начнёт выполняться заданное
 *   условие и если условие так и не выполнилось - валят тест.
 *
 * @package Common\Module\WFramework\WebObjects\Base\Interfaces
 */
interface IPageObject
{
    /**
     * Возвращает своего родителя
     *
     * Если родителя нет, то возвращает EmptyComposite
     *
     * @return IPageObject
     */
    public function getParent();

    /**
     * Возвращает ассоциативный массив детей
     *
     * Массив имеет вид: имя Composite => Composite
     *
     * @return IPageObject[]
     */
    public function getChildren() : array;

    public function __toString();

    public function getClass();

    public function getClassShort();

    public function getLocator() : WLocator;

    /**
     * PageObject должен существовать.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен присутствовать в коде страницы.
     * Если флаг $deep - стоит в true, то и все PageObject'ы, которые объявлены внутри этого PageObject'а,
     * тоже должны существовать.
     *
     * Если хотя бы для одного PageObject'а условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
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
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
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
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
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
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
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
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
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
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeDisabled(bool $deep = true);

    /**
     * PageObject должен содержать заданный текст.
     *
     * Для этого элемент на который ссылается локатор PageObject'а должен содержать заданный текст, начиная с любого
     * места и в любом регистре.
     *
     * Т.е. строка "блаблаблазаданный текстблаблабла" - содержит "заданный текст".
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
     *
     * @return static
     */
    public function shouldContainText(string $text);

    /**
     * PageObject должен быть не только виден, но и находиться внутри рамок окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeInViewport(bool $deep = true);

    /**
     * PageObject должен находиться за рамками окна браузера
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout
     * для наследников WElement, collectionTimeout для наследников WElements) - валит тест.
     *
     * @param bool $deep
     * @return static
     */
    public function shouldBeOutOfViewport(bool $deep = true);


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
    public function isContainingText(string $text) : bool;

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

    public function getProxyWebElement() : ProxyWebElement;
}
