<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 06.03.19
 * Time: 17:14
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Conditions\CountEmpty;
use Codeception\Lib\WFramework\Conditions\CountEquals;
use Codeception\Lib\WFramework\Conditions\CountGreaterThanOrEqual;
use Codeception\Lib\WFramework\Conditions\CountLessThanOrEqual;
use Codeception\Lib\WFramework\Conditions\SomeElement_;
use Codeception\Lib\WFramework\Conditions\Text;
use Codeception\Lib\WFramework\Conditions\TextContains;
use Codeception\Lib\WFramework\Conditions\Value;
use Codeception\Lib\WFramework\Conditions\ValueContains;
use Codeception\Lib\WFramework\Operations\Get\GetTextRaw;
use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\Operations\Get\GetValue;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElements;
use Codeception\Lib\WFramework\WebObjects\Base\Traits\PageObjectBaseMethods;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Sequence;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\Helpers\Composite;
use Ds\Stack;
use Ds\Traits\GenericSequence;
use Ds\Vector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use function array_keys;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import\WsFrom;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Ds\Map;
use function implode;
use function is_callable;

/**
 * Данный класс реализует механизм работы с коллекцией веб-элементов.
 *
 * WElement (веб-элемент) описывает одиночный элемент на странице, например: кнопку, надпись, поле ввода.
 *
 * Но иногда на странице имеется группа веб-элементов, которую можно найти по одному локатору и которую можно назвать
 * одним словом, например: строки таблицы, ячейки строки, пункты меню, список открытых вкладок и т.д.
 *
 * Такая группа не содержит фиксированное число элементов: в таблице может отображаться сколько угодно строк, хоть 0,
 * хоть 10000. А дать название каждому элементу в группе затруднительно и не имеет смысла.
 *
 * Чтобы не объявлять внутри PageObject'а 10000 полей с именами:
 * строка таблицы 1, строка таблицы 2, ..., строка таблицы 10000
 * и создан механизм WCollection.
 *
 * Наследником WCollection является WArray - который описывает простой список веб-элементов.
 *
 * Допустим у нас в PageObject'е есть поле:
 *
 * $row = WTableRow::fromXpath('строка таблицы', "./div/div/div");
 *
 * которое ссылается на первую строку таблицы.
 *
 * Чтобы сделать из него коллекцию, которая описывает все строки таблицы - достаточно обернуть объявление этого поля в
 * вызов WArray::fromFirstElement(), вот так:
 *
 * $rows = WArray::fromFirstElement(WTableRow::fromXpath('строка таблицы', "./div/div/div"));
 *
 * Теперь нужно обновить коллекцию веб-элементов, чтобы она подгрузила все строки таблицы со страницы:
 *
 * $rows->refresh();
 *
 * И вызвав getElementsArray() мы получим массив строк таблицы, каждая из которых будет обёрнута в класс WTableRow:
 *
 * foreach ($rows->getElementsArray() as $tableRow)
 * {
 *      $tableRow->shouldBeDisplayed();
 * }
 *
 * WCollection так же реализует стандартный интерфейс PageObject'а и может перенаправлять вызовы его методов всем элементам
 * своей коллекции.
 *
 * Т.е. вместо, приведённого выше, вызова foreach, можно просто сделать:
 *
 * $rows->shouldBeDisplayed();
 *
 * @package Common\Module\WFramework\WebObjects\Base\WCollection
 */
abstract class WCollection extends Composite implements IPageObject
{
    use PageObjectBaseMethods;

    /** @var ProxyWebElements|null  */
    protected $proxyWebElements = null;

    /** @var WElement|null */
    protected $firstElement = null;

    protected $proxyWebElementsStateId = 0;

    /** @var bool */
    protected $filtered = false;

    /** @var Stack */
    protected $filteredOutItems;

    /**
     * Создаёт коллекцию веб-элементов из объявления одиночного элемента.
     *
     * Для этого локатор элемента должен быть достаточно общим чтобы по нему находилось несколько таких элементов.
     *
     * Все элементы коллекции будут иметь такой же класс, как и элемент из которого они созданы.
     *
     * @param WElement $webElement
     * @return static
     */
    public static function fromFirstElement(WElement $webElement)
    {
        return new static(WsFrom::firstElement($webElement));
    }

    public static function fromProxyWebElements(string $instanceName, ProxyWebElements $proxyWebElements, string $elementClass)
    {
        return new static(WsFrom::proxyWebElements($instanceName, $proxyWebElements, $elementClass));
    }

    public function __construct(WsFrom $importer)
    {
        parent::__construct();

        $this->proxyWebElements = $importer->getProxyWebElements();
        $this->firstElement = $importer->getFirstElement();

        $this->name = 'Коллекция элементов: ' . $this->firstElement->getName();

        $this->filtered = false;
        $this->filteredOutItems = new Stack();
    }

    public function setParent(Composite $parent)
    {
        parent::setParent($parent);

        // На момент создания коллекции она ещё не знает о своём родителе, равно, как и её firstElement
        // Поэтому когда родитель прописывается в коллекции заодно прописываем его родителем firstElement
        $this->firstElement->setParent($parent);
    }

    public function __toString() : string
    {
        if ($this->isRelative() && !$this->getParent() instanceof EmptyComposite)
        {
            return $this->getParent() . ' / ' . $this->getClassShort() . ' (' . $this->getName() . ')';
        }

        return '/ ' . $this->getClassShort() . ' (' . $this->getName() . ')';
    }

    public function returnSeleniumElements() : ProxyWebElements
    {
        if ($this->proxyWebElements === null)
        {
            if ($this->isRelative())
            {
                /**
                 * WCollection является специальным механизмом для создания коллекции элементов.
                 * Он не является PageObject'ом поэтому не может выступать родителем для других
                 * PageObject'ов. Вместо этого он назначает их родителем PageObject в котором он
                 * объявлен.
                 */

                $this->proxyWebElements = new ProxyWebElements($this->getLocator(), $this->getParent()->returnSeleniumServer(), $this->getTimeout(), $this->getParent()->returnSeleniumElement());
            }
            else
            {
                $this->proxyWebElements = new ProxyWebElements($this->getLocator(), $this->getParent()->returnSeleniumServer(), $this->getTimeout());
            }
        }

        return $this->proxyWebElements;
    }

    public function returnSeleniumServer() : RemoteWebDriver
    {
        return $this->getParent()->returnSeleniumServer();
    }

    public function returnCodeceptionActor()
    {
        return $this->getParent()->returnCodeceptionActor();
    }

    /**
     * Данный метод заполняет коллекцию элементами со страницы.
     *
     * !!! В целях оптимизации коллекция не заполняет себя без явного вызова данного метода.
     *
     * Этот метод следует вызывать при первом обращении к коллекции или если количество элементов на странице изменилось.
     *
     * @return $this
     */
    public function refresh()
    {
        WLogger::logAction($this, "обновляем содержимое");

        $this->returnSeleniumElements()->refresh();
        $this->updateFromProxyWebElements();

        $this->filtered = false;
        $this->filteredOutItems->clear();

        return $this;
    }

    /**
     * После обновления коллекции через refresh() с помощью этого метода можно выгрузить лишние элементы,
     * которые не подпадают под заданные условия.
     *
     * @param AbstractCondition ...$conditions
     *
     * @return $this
     */
    public function filterLoadedItems(AbstractCondition ...$conditions)
    {
        $conditionDescription = [];

        foreach ($conditions as $condition)
        {
            $conditionDescription []= (string) $condition;
        }

        WLogger::logAction($this, "отфильтровываем загруженные элементы которые не соответствуют условиям: " . implode(', ', $conditionDescription));

        $conditionsPassed = function (IPageObject $element) use ($conditions) {
            foreach ($conditions as $condition)
            {
                if (!$element->is($condition))
                {
                    return false;
                }
            }

            return true;
        };


        foreach ($this->getChildren() as $name => $child)
        {
            if ($conditionsPassed($child))
            {
                continue;
            }

            $this->removeChild($name);
            $this->filteredOutItems->push($child);
        }

        if (!$this->filteredOutItems->isEmpty())
        {
            $this->filtered = true;
        }

        return $this;
    }

    public function filtersReset()
    {
        WLogger::logAction($this, "снимаем фильтры с загруженных элементов");

        while (!$this->filteredOutItems->isEmpty())
        {
            $this->addChild($this->filteredOutItems->pop());
        }

        $this->filtered = false;

        return $this;
    }

    /**
     * Заполняет коллекцию элементами из низлежащего объекта ProxyWebElements
     *
     * @param ProxyWebElements $proxyWebElements
     */
    private function updateFromProxyWebElements()
    {
        if ($this->returnSeleniumElements()->getInnerStateId() === $this->proxyWebElementsStateId)
        {
            return;
        }

        $this->proxyWebElementsStateId = $this->returnSeleniumElements()->getInnerStateId();

        $this->clearChildren();

        foreach ($this->returnSeleniumElements()->getElementsArray() as $index => $proxyWebElement)
        {
            $webElement = $this->wrapInPageObject($index, $proxyWebElement);

            $this->addChild($webElement);
            $webElement->setParent($this->getParent());
        }
    }

    protected function wrapInPageObject(int $index, ProxyWebElement $proxyWebElement) : WElement
    {
        return $this->firstElement->getClass()::fromProxyWebElement($this->firstElement->getName() . " [$index]", $proxyWebElement, $this->getParent());
    }

    public function getChildren() : Map
    {
        $this->updateFromProxyWebElements();

        return parent::getChildren();
    }

    public function getLocator() : WLocator
    {
        return $this->firstElement->getLocator();
    }

    public function isRelative() : bool
    {
        return $this->firstElement->isRelative();
    }

    /**
     * Данный метод возвращает массив элементов коллекции
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @return WElement[]|Sequence
     */
    public function getElementsArray() : Sequence
    {
        WLogger::logAction($this , "получаем массив элементов");

        return $this->getChildren()->values();
    }

    /**
     * Данный метод возвращает ассоциативный массив элементов коллекции
     *
     * Ключом в массиве будет значение поля или результат вызова метода указанного в $methodOrProperty,
     * а значением - соответствующий элемент.
     *
     * Если $preserveDuplicates = false и несколько элементов имеют одно и то же значение указанного поля, или одинаковый
     * результат вызова метода - то в массив попадёт первый из этих элементов.
     *
     * Если $preserveDuplicates = true - то все элементы с дублирующимися ключами попадут в результирующий массив. Для этого
     * к ключу будет приписан суффикс _0, _1, _2 и т.д. Это будет сделано посредством неэффективного алгоритма так что без
     * нужды эту опцию использовать не стоит.
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @param string $methodOrProperty
     * @param bool $preserveDuplicates
     * @return WElement[]|Map
     */
    public function getElementsMap(string $methodOrProperty, bool $preserveDuplicates = false) : Map
    {
        WLogger::logAction($this, "получаем ассоциативный массив элементов, где ключи будут получены путём вызова: $methodOrProperty - для каждого элемента коллекции");

        $elements = $this->getElementsArray();

        $result = [];

        $sameKeysCount = [];

        $changeKey = static function ($array, $oldKey, $newKey)
        {
            $keys = array_keys($array);
            $keys[array_search($oldKey, $keys)] = $newKey;

            return array_combine($keys, $array);
        };

        foreach ($elements as $element)
        {
            if ($element->isNotExist())
            {
                continue;
            }

            $key = null;

            if (is_callable([$element, $methodOrProperty]))
            {
                $key = $element->$methodOrProperty();
            }
            else if (isset($element->$methodOrProperty))
            {
                $key = $element->$methodOrProperty;
            }

            if ($key === null)
            {
                continue;
            }

            if (trim($key) === '')
            {
                $key = '';
            }

            if (!isset($result[$key]) || $preserveDuplicates === false)
            {
                $result[$key] = $element;
                continue;
            }

            if (!isset($sameKeysCount[$key]))
            {
                $sameKeysCount[$key] = 0;

                $result = $changeKey($result, $key, "{$key}_0");
            }

            $sameKeysCount[$key] += 1;

            $result["{$key}_{$sameKeysCount[$key]}"] = $element;
        }

        return new Map($result);
    }

    public function count() : int
    {
        return $this->getChildren()->count();
    }

    public function isEmpty() : bool
    {
        return $this->is(new CountEmpty(), false);
    }

    /**
     * Возвращает первый элемент коллекции
     */
    public function getFirstElement() : WElement
    {
        WLogger::logAction($this, 'получаем первый элемент');

        if ($this->filtered)
        {
            return $this->getElementsArray()->first();
        }

        return $this->firstElement;
    }

    /**
     * Возвращает последний элемент коллекции
     */
    public function getLastElement() : WElement
    {
        WLogger::logAction($this, 'получаем последний элемент');

        return $this->getElementsArray()->last();
    }

    public function getElement(int $index) : WElement
    {
        WLogger::logAction($this, "получаем элемент по индексу: $index");

        $elementsArray = $this->getElementsArray();

        if (!isset($elementsArray[$index]))
        {
            throw new UsageException($this . ' - не содержит элемента по индексу: ' . $index);
        }

        return $elementsArray[$index];
    }

    public function hasElement(int $index) : bool
    {
        WLogger::logAction($this, "содержит элемент по индексу: $index?");

        return isset($elementsArray[$index]);
    }

    /**
     * @return EmptyComposite|WPageObject
     * @throws UsageException
     */
    public function getParent()
    {
        $parent = parent::getParent();

        if (!$parent instanceof EmptyComposite && !$parent instanceof WPageObject)
        {
            throw new UsageException($this . ' -> родителем WCollection должен быть наследник WPageObject или EmptyComposite');
        }

        return $parent;
    }

    public function getTimeout() : int
    {
        return (int) TestProperties::getValue('collectionTimeout');
    }




    /**
     * Возвращает массив видимых текстов всех элементов коллекции
     * @return Sequence
     */
    public function getVisibleTexts() : Sequence
    {
        WLogger::logAction($this, "получаем видимые тексты всех элементов коллекции");

        /** @var Sequence $result */
        $result = $this->accept(new GetText());

        $texts = implode(', ', $result->toArray());

        WLogger::logDebug($this, "имеет видимые тексты: '$texts'");

        return $result;
    }

    /**
     * Возвращает массив сырых текстов (включая невидимые тексты) всех элементов коллекции
     *
     * @return Sequence
     */
    public function getAllTexts() : Sequence
    {
        WLogger::logAction($this, "получаем все тексты (включая невидимые) всех элементов коллекции");

        /** @var Sequence $result */
        $result = $this->accept(new GetTextRaw());

        $texts = implode(', ', $result->toArray());

        WLogger::logDebug($this, "имеет все тексты: '$texts'");

        return $result;
    }

    public function getValues() : Sequence
    {
        WLogger::logAction($this, "получаем значение атрибута value всех элементов коллекции");

        /** @var Sequence $result */
        $result = $this->accept(new GetValue());

        $values = implode(', ', $result->toArray());

        WLogger::logDebug($this, "имеет значения: '$values'");

        return $result;
    }




    /**
     * Коллеция должна содержать >= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return $this
     */
    public function shouldBeGreaterThanOrEqual(int $size)
    {
        return $this->should(new CountGreaterThanOrEqual($size), false);
    }

    /**
     * Коллеция должна содержать == заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return $this
     */
    public function shouldBeEqual(int $size)
    {
        return $this->should(new CountEquals($size), false);
    }

    /**
     * Коллеция должна содержать <= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return $this
     */
    public function shouldBeLesserThanOrEqual(int $size)
    {
        return $this->should(new CountLessThanOrEqual($size), false);
    }

    /**
     * Коллеция должна содержать >= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return bool
     */
    public function finallyGreaterThanOrEqual(int $size) : bool
    {
        return $this->finally_(new CountGreaterThanOrEqual($size), false);
    }

    /**
     * Коллеция должна содержать == заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return bool
     */
    public function finallyEqual(int $size) : bool
    {
        return $this->finally_(new CountEquals($size), false);
    }

    /**
     * Коллеция должна содержать <= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return bool
     */
    public function finallyLesserThanOrEqual(int $size) : bool
    {
        return $this->finally_(new CountLessThanOrEqual($size), false);
    }

    /**
     * Коллеция содержит >= заданного числа элементов?
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @return bool
     */
    public function isGreaterThanOrEqual(int $size) : bool
    {
        return $this->is(new CountGreaterThanOrEqual($size), false);
    }

    /**
     * Коллеция содержит == заданного числа элементов?
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @return bool
     */
    public function isEqual(int $size) : bool
    {
        return $this->is(new CountEquals($size), false);
    }

    /**
     * Коллеция содержит <= заданного числа элементов?
     *
     * Ничего не ожидает. Возвращает true или false.
     *
     * @return bool
     */
    public function isLesserThanOrEqual(int $size) : bool
    {
        return $this->is(new CountLessThanOrEqual($size), false);
    }




    public function shouldHaveText(string $text)
    {
        return $this->should(new SomeElement_(new Text($text)), false);
    }

    public function shouldContainText(string $text)
    {
        return $this->should(new SomeElement_(new TextContains($text)), false);
    }

    public function shouldHaveValue(string $value)
    {
        return $this->should(new SomeElement_(new Value($value)), false);
    }

    public function shouldContainValue(string $value)
    {
        return $this->should(new SomeElement_(new ValueContains($value)), false);
    }




    public function finallyHaveText(string $text) : bool
    {
        return $this->finally_(new SomeElement_(new Text($text)), false);
    }

    public function finallyContainText(string $text) : bool
    {
        return $this->finally_(new SomeElement_(new TextContains($text)), false);
    }

    public function finallyHaveValue(string $value) : bool
    {
        return $this->finally_(new SomeElement_(new Value($value)), false);
    }

    public function finallyContainValue(string $value) : bool
    {
        return $this->finally_(new SomeElement_(new ValueContains($value)), false);
    }




    public function isHaveText(string $text) : bool
    {
        return $this->is(new SomeElement_(new Text($text)), false);
    }

    public function isContainText(string $text) : bool
    {
        return $this->is(new SomeElement_(new TextContains($text)), false);
    }

    public function isHaveValue(string $value) : bool
    {
        return $this->is(new SomeElement_(new Value($value)), false);
    }

    public function isContainValue(string $value) : bool
    {
        return $this->is(new SomeElement_(new ValueContains($value)), false);
    }
}
