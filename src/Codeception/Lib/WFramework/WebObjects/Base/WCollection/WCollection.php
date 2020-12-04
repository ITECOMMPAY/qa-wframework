<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 06.03.19
 * Time: 17:14
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection;


use Codeception\Lib\WFramework\ProxyWebElement\ProxyWebElement;
use function array_values;
use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\Helpers\Composite;
use function array_keys;
use Codeception\Lib\WFramework\Debug\DebugHelper;
use Codeception\Lib\WFramework\Debug\DebugInfo;
use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Exceptions\FacadeWebElementOperations\WaitUntilElement;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElementsListener;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import\WsFrom;
use Codeception\Lib\WFramework\WLocator\WLocator;
use function end;
use PHPUnit\Framework\AssertionFailedError;
use function implode;
use function is_callable;
use function reset;

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
abstract class WCollection extends Composite implements IPageObject, FacadeWebElementsListener
{
    /** @var FacadeWebElements|null  */
    protected $facadeWebElements = null;

    /** @var string */
    protected $instanceName = '';

    /** @var WLocator */
    protected $locator = null;

    /** @var bool */
    protected $relative = true;

    /** @var WElement|string */
    protected $elementClass = '';

    protected $filtered = False;

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

    public static function fromFacadeWebElements(string $instanceName, FacadeWebElements $facadeWebElements, string $elementClass)
    {
        return new static(WsFrom::facadeWebElements($instanceName, $facadeWebElements, $elementClass));
    }

    public function __construct(WsFrom $importer)
    {
        parent::__construct();

        $this->facadeWebElements = $importer->getFacadeWebElements();
        $this->instanceName = $importer->getInstanceName();
        $this->locator = $importer->getLocator();
        $this->relative = $importer->getRelative();
        $this->elementClass = $importer->getElementClass();

        if ($this->facadeWebElements !== null)
        {
            $this->facadeWebElements->listenerAdd($this);
        }

        $this->name = 'Коллекция элементов: ' . $this->instanceName;
    }

    public function __toString() : string
    {
        if ($this->relative && !$this->getParent() instanceof EmptyComposite)
        {
            return $this->getParent() . ' / ' . $this->getClassShort() . ' (' . $this->getName() . ')';
        }

        return '/ ' . $this->getClassShort() . ' (' . $this->getName() . ')';
    }


    public function returnSeleniumElements() : FacadeWebElements
    {
        WLogger::logInfo($this . ' -> обращаемся к низлежащему API');

        if ($this->facadeWebElements === null)
        {
            if ($this->relative === True)
            {
                /**
                 * WCollection является специальным механизмом для создания коллекции элементов.
                 * Он не является PageObject'ом поэтому не может выступать родителем для других
                 * PageObject'ов. Вместо этого он назначает их родителем PageObject в котором он
                 * объявлен.
                 */
                $parent = $this->getParent()->returnSeleniumElement();

                $this->facadeWebElements = FacadeWebElements::fromLocator($this->locator, $this->getParent()->returnSeleniumServer(), $parent);
            }
            else
            {
                $this->facadeWebElements = FacadeWebElements::fromLocator($this->locator, $this->getParent()->returnSeleniumServer());
            }

            $this->facadeWebElements->listenerAdd($this);
        }

        if (!$this->facadeWebElements->returnProxyWebElements()->hasDebugInfo())
        {
            $debugInfo = (new DebugInfo())->setPageObject($this->getParent());
            $this->facadeWebElements->returnProxyWebElements()->setDebugInfo($debugInfo);
        }

        return $this->facadeWebElements;
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
        WLogger::logInfo($this . " -> обновляем содержимое");

        $this->returnSeleniumElements()->refresh();

        return $this;
    }

    /**
     * Фильтрует коллекцию элементов по условию
     *
     * @param Cond $elementFilter
     * @return $this
     */
    public function filtersSet(Cond $elementFilter)
    {
        WLogger::logInfo($this . " -> задаём фильтрацию по условию: " . $elementFilter->getName());

        $this->filtered = True;

        $this->returnSeleniumElements()->filtersSet($elementFilter);

        return $this;
    }

    /**
     * Добавляет условие к списку фильтров данной коллекции
     *
     * @param Cond $elementFilter
     * @return $this
     */
    public function filterAdd(Cond $elementFilter)
    {
        WLogger::logInfo($this . " -> добавляем фильтрацию по условию: " . $elementFilter->getName());

        $this->filtered = True;

        $this->returnSeleniumElements()->filterAdd($elementFilter);

        return $this;
    }

    /**
     * Удаляет последний применённый фильтр
     *
     * @return $this
     */
    public function filterPop()
    {
        WLogger::logInfo($this . " -> удаляем последний добавленный фильтр");

        if ($this->returnSeleniumElements()->filterPop()->filtersGet() === null)
        {
            $this->filtered = False;
        }

        return $this;
    }

    /**
     * Удаляет все фильтры
     *
     * @return $this
     */
    public function filtersRemove()
    {
        WLogger::logInfo($this . " -> удаляем все фильтры");

        $this->filtered = False;

        $this->returnSeleniumElements()->filtersRemove();

        return $this;
    }

    /**
     * Это внутренний метод, который служит для уведомления коллекции, что лежащий под её капотом FacadeWebElements
     * обновился.
     *
     * В тестах его использовать не нужно.
     *
     * По сути, во время вызова WCollection->refresh() происходит вызов FacadeWebElements->refresh(), который
     * вызывает ProxyWebElements->refresh(), который вызывает низкоуровневый код Селениума, который подгружает
     * элементы со страницы. Эти элементы оборачиваются в ProxyWebElement, которые затем передаются в метод
     * FacadeWebElements->fillFrom(), который оборачивает каждый ProxyWebElement в FacadeWebElement и затем
     * передаёт их в метод WCollection->fillFrom(), который оборачивает каждый FacadeWebElement в заданный
     * WElement класс и добавляет в список своих детей.
     */
    public function onFacadeWebElementsRefresh()
    {
        $this->fillFrom($this->facadeWebElements);
    }

    /**
     * Заполняет коллекцию элементами из низлежащего объекта FacadeWebElements
     *
     * @param FacadeWebElements $facadeWebElements
     */
    private function fillFrom(FacadeWebElements $facadeWebElements)
    {
        $this->clearChildren();

        $facadeWebElementsArray = $facadeWebElements->getElementsArray();
        $count = count($facadeWebElementsArray);

        for ($i = 0; $i < $count; $i++)
        {
            $facadeWebElement = $facadeWebElementsArray[$i];

            /** @var WElement $webElement */
            $webElement = $this->elementClass::fromFacadeWebElement($this->instanceName . " [$i]", $facadeWebElement);
            $this->addChild($webElement);
            $webElement->setParent($this->getParent());
        }
    }

    public function getLocator() : WLocator
    {
        return $this->locator;
    }

    /**
     * Данный метод возвращает массив элементов коллекции
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @return WElement[]
     */
    public function getElementsArray() : array
    {
        WLogger::logInfo($this . " -> получаем массив элементов");

        return array_values($this->getChildren());
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
     * @return WElement[]
     */
    public function getElementsMap(string $methodOrProperty, bool $preserveDuplicates = false) : array
    {
        WLogger::logInfo($this . " -> получаем ассоциативный массив элементов, где ключи будут получены путём вызова: $methodOrProperty - для каждого элемента коллекции");

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

        return $result;
    }

    /**
     * Возвращает первый элемент коллекции
     */
    public function getFirstElement() : WElement
    {
        WLogger::logInfo($this . ' -> получаем первый элемент');

        $elements = $this->getElementsArray();

        if (empty($elements))
        {
            throw new UsageException('Перед вызовом getFirstElement() нужно быть уверенным, что коллекция содержит хотя бы один элемент');
        }

        return reset($elements);
    }

    /**
     * Возвращает последний элемент коллекции
     */
    public function getLastElement() : WElement
    {
        WLogger::logInfo($this . ' -> получаем последний элемент');

        $elements = $this->getElementsArray();

        if (empty($elements))
        {
            throw new UsageException('Перед вызовом getLastElement() нужно быть уверенным, что коллекция содержит хотя бы один элемент');
        }

        return end($elements);
    }

    public function count() : int
    {
        return count($this->getChildren());
    }

    /**
     * @param string $description
     * @throws UsageException|AssertionFailedError
     */
    protected function fail(string $description = '')
    {
        $this
            ->getParent()
            ->returnCodeceptionActor()
            ->fail($this . PHP_EOL . ' -> ' . $description)
            ;
    }

    /**
     * Ждёт выполнение заданного условия для данной коллекции элементов.
     *
     * Если условие не было выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @param CCond $condition - условие
     * @param string $description - описание причины, по которой заданное условие должно выполняться для данной коллекции элементов
     * @param callable|null $debugHandler - опциональная функция, которая продиагностирует почему условие не выполнилось
     *                                      и сообщит тестировщику в удобном для понимания виде
     * @return $this
     * @throws UsageException|AssertionFailedError
     */
    protected function should(CCond $condition, string $description = '', callable $debugHandler = null)
    {
        WLogger::logInfo($this . ' -> ' . $description);

        try
        {
            $this
                ->returnSeleniumElements()
                ->wait()
                ->until($condition)
                ;
        }
        catch (WaitUntilElement $e)
        {
            $message = $condition->toString();

            if ($debugHandler !== null)
            {
                $debugInfo = (new DebugInfo())->setPageObject($this);
                $message .= PHP_EOL . $debugHandler($debugInfo);
            }

            $this->fail($message);
        }

        return $this;
    }

    protected function eachElementShould(string $shouldMethod)
    {
        foreach ($this->getElementsArray() as $element)
        {
            $element->$shouldMethod();
        }

        return $this;
    }

    protected function firstElementShould(string $shouldMethod)
    {
        $this->getFirstElement()->$shouldMethod();

        return $this;
    }

    public function shouldExist(bool $deep = false)
    {
        return $this->should(CCond::sizeGreaterThan(0), 'должен существовать хотя бы один элемент', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST);});
    }

    public function shouldNotExist(bool $deep = false)
    {
        return $this->should(CCond::size(0), 'должен быть пустой', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::NOT_EXIST);});
    }

    public function shouldBeDisplayed(bool $deep = false)
    {
        $this->shouldExist();

        if ($deep)
        {
            return $this->eachElementShould('shouldBeDisplayed');
        }

        return $this->firstElementShould('shouldBeDisplayed');
    }

    public function shouldBeHidden(bool $deep = false)
    {
        try
        {
            $this->shouldNotExist();
        }
        catch (AssertionFailedError $e)
        {
            if ($deep)
            {
                return $this->eachElementShould('shouldBeHidden');
            }

            return $this->firstElementShould('shouldBeHidden');
        }

        return $this;
    }

    public function shouldBeEnabled(bool $deep = false)
    {
        $this->shouldExist();

        if ($deep)
        {
            return $this->eachElementShould('shouldBeEnabled');
        }

        return $this->firstElementShould('shouldBeEnabled');
    }

    public function shouldBeDisabled(bool $deep = false)
    {
        $this->shouldExist();

        if ($deep)
        {
            return $this->eachElementShould('shouldBeDisabled');
        }

        return $this->firstElementShould('shouldBeDisabled');
    }

    public function shouldBeInViewport(bool $deep = true)
    {
        $this->shouldExist();

        if ($deep)
        {
            return $this->eachElementShould('shouldBeInViewport');
        }

        return $this->firstElementShould('shouldBeInViewport');
    }

    public function shouldBeOutOfViewport(bool $deep = true)
    {
        $this->shouldExist();

        if ($deep)
        {
            return $this->eachElementShould('shouldBeOutOfViewport');
        }

        return $this->firstElementShould('shouldBeOutOfViewport');
    }

    public function shouldContainText(string $text)
    {
        return $this->should(CCond::textsInAnyOrder(), "должен содержать текст: $text");
    }

    /**
     * Коллеция должна содержать >= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return static
     */
    public function shouldBeGreaterThanOrEqual(int $size)
    {
        return $this->should(CCond::sizeGreaterThanOrEqual($size), "должна иметь больше или равно $size элементов");
    }

    /**
     * Коллеция должна содержать == заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return static
     */
    public function shouldBeEqual(int $size)
    {
        return $this->should(CCond::size($size), "должна иметь ровно $size элементов");
    }

    /**
     * Коллеция должна содержать <= заданного числа элементов.
     *
     * Если условие не будет выполнено в течении заданного таймаута (collectionTimeout) - валит тест.
     *
     * @return static
     */
    public function shouldBeLesserThanOrEqual(int $size)
    {
        return $this->should(CCond::sizeLessThanOrEqual($size), "должна иметь меньше или равно $size элементов");
    }

    protected function is(CCond $condition, string $description) : bool
    {
        WLogger::logInfo($this . ' -> ' . $description);

        return $this
                    ->refresh()
                    ->returnSeleniumElements()
                    ->checkIt()
                    ->is($condition)
                    ;
    }

    protected function eachElementIs(string $isMethod) : bool
    {
        foreach ($this->getElementsArray() as $element)
        {
            if (!$element->$isMethod())
            {
                return false;
            }
        }

        return true;
    }

    protected function firstElementIs(string $isMethod) : bool
    {
        return $this->getFirstElement()->$isMethod();
    }

    public function isExist(bool $deep = false) : bool
    {
        return $this->is(CCond::sizeGreaterThan(0), 'существует хотя бы один элемент?');
    }

    public function isNotExist(bool $deep = false) : bool
    {
        return $this->is(CCond::size(0), 'пустая?');
    }

    public function isDisplayed(bool $deep = false) : bool
    {
        if ($this->isNotExist())
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachElementIs('isDisplayed');
        }

        return $this->firstElementIs('isDisplayed');
    }

    public function isHidden(bool $deep = false) : bool
    {
        if ($this->isNotExist())
        {
            return true;
        }

        if ($deep)
        {
            return $this->eachElementIs('isHidden');
        }

        return $this->firstElementIs('isHidden');
    }

    public function isEnabled(bool $deep = false) : bool
    {
        if ($this->isNotExist())
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachElementIs('isEnabled');
        }

        return $this->firstElementIs('isEnabled');
    }

    public function isDisabled(bool $deep = false) : bool
    {
        if ($this->isNotExist())
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachElementIs('isDisabled');
        }

        return $this->firstElementIs('isDisabled');
    }

    public function isContainingText(string $text) : bool
    {
        return $this->is(CCond::textsInAnyOrder($text), "содержит текст: '$text'?");
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
        return $this->is(CCond::sizeGreaterThanOrEqual($size), "имеет больше или равно $size элементов?");
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
        return $this->is(CCond::sizeGreaterThanOrEqual($size), "имеет ровно $size элементов?");
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
        return $this->is(CCond::sizeLessThanOrEqual($size), "имеет меньше или равно $size элементов?");
    }

    public function isInViewport(bool $deep = true) : bool
    {
        if ($this->isNotExist())
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachElementIs('isInViewport');
        }

        return $this->firstElementIs('isInViewport');
    }

    public function isOutOfViewport(bool $deep = true) : bool
    {
        if ($this->isNotExist())
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachElementIs('isOutOfViewport');
        }

        return $this->firstElementIs('isOutOfViewport');
    }



    /**
     * Возвращает массив видимых текстов всех элементов коллекции
     * @return array
     */
    public function getVisibleTexts() : array
    {
        WLogger::logInfo($this . " -> получаем видимые тексты всех элементов коллекции");

        $result = $this
                        ->returnSeleniumElements()
                        ->get()
                        ->texts()
                        ;

        $texts = implode(', ', $result);

        WLogger::logInfo($this . " -> имеет видимые тексты: '$texts'");

        return $result;
    }

    /**
     * Возвращает массив сырых текстов (включая невидимые тексты) всех элементов коллекции
     *
     * @return array
     */
    public function getAllTexts() : array
    {
        WLogger::logInfo($this . " -> получаем все тексты (включая невидимые) всех элементов коллекции");

        $result = $this
                        ->returnSeleniumElements()
                        ->get()
                        ->rawTexts()
                        ;

        $texts = implode(', ', $result);

        WLogger::logInfo($this . " -> имеет все тексты: '$texts'");

        return $result;
    }
}
