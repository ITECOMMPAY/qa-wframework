<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 16:05
 */

namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Deque;
use Ds\Map;
use Ds\Sequence;
use Ds\Vector;
use function implode;

/**
 * Данный класс реализует паттерн Компоновщик (https://ru.wikipedia.org/wiki/Компоновщик_(шаблон_проектирования)).
 *
 * Благодаря нему все PageObject'ы можно организовать в древовидные структуры. Корнями этих структур выступают наследники
 * WBlock, которые описывают некие логические блоки веб-страницы, например: панели, окна и группы данных.
 * WBlock разбивает свой кусочек страницы на простые части, например: поля ввода, кнопки, таблицы, списки, календари.
 * Эти части наследуют от WElement.
 * WElement может делить себя на ещё более мелкие части, которые описаны в других WElement. Например, таблицу можно разделить
 * на заголовок и тело, заголовок состоит из кнопок - заглавий колонок, а тело состоит из строк. Каждая строка состоит из ячеек.
 * Каждая ячейка является кнопкой или надписью.
 *
 * Зачем так делать?
 * Во-первых, потому что веб-страница сама по себе является DOM-деревом. Так что логично описывать дерево с помощью дерева.
 * Во-вторых, потому что React - самый популярный фреймворк для создания веб-интерфейсов - именно так описывает веб-страницу.
 *
 * Разбивая интерфейс на объекты помельче можно описать веб-страницу любой сложности.
 *
 * @package Common\Module\WFramework\WebObjects\Base
 */
abstract class Composite extends ModernObject
{
    /** @var Composite */
    private $parent = null;

    /** @var Composite[]|Map */
    private $children;

    /** @var string  */
    protected $name = '';

    /**
     * Возвращает своё имя.
     *
     * @return string
     */
    public function getName() : string
    {
        if (empty($this->name))
        {
            throw new UsageException('Наследники Composite должны задавать поле $name уникальным значением');
        }

        return $this->name;
    }

    /**
     * Задаёт элемент, как своего родителя
     *
     * @param Composite $parent
     */
    public function setParent(Composite $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Возвращает своего родителя
     *
     * Если родителя нет, то возвращает EmptyComposite
     *
     * @return Composite
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Добавляет все прописанные в данном классе наследники Composite себе в дети
     */
    private function registerChildren()
    {
        foreach ($this as $fieldName => $fieldValue)
        {
            if ($fieldName !== 'parent' && ($fieldValue instanceof Composite))
            {
                if ($fieldName[0] === '_')
                {
                    $fieldValue->setParent($this);
                    continue;
                }

                $this->addChild($fieldValue);
            }
        }
    }

    /**
     * Добавляет ребёнка себе в дети и регистрирует себя, как его родителя
     *
     * Ребёнок должен иметь уникальное имя.
     *
     * @param Composite $child
     */
    public function addChild(Composite $child)
    {
        $this->children->put($child->getName(), $child);
        $child->setParent($this);
    }

    public function removeChild(string $name)
    {
        $this->children->remove($name);
    }

    public function addChildren(Composite ...$children)
    {
        foreach ($children as $child)
        {
            $this->addChild($child);
        }
    }

    /**
     * Очищает массив своих детей
     */
    public function clearChildren()
    {
        $this->children->clear();
    }

    /**
     * Возвращает ассоциативный массив детей
     *
     * Массив имеет вид: имя Composite => Composite
     *
     * @return Composite[]|Map
     */
    public function getChildren() : Map
    {
        return $this->children;
    }

    public function hasChild(string $name) : bool
    {
        return $this->children->hasKey($name);
    }

    /**
     * Возвращает своего ребёнка по его имени
     *
     * @param string $name - имя
     * @return Composite|WPageObject
     * @throws UsageException
     */
    public function getChildByName(string $name)
    {
        if (!$this->hasChild($name))
        {
            throw new UsageException("У узла нет ребёнка с именем: $name, есть только: " . implode(', ', $this->children->keys()));
        }

        return $this->children->get($name);
    }

    public function __construct()
    {
        $this->parent = EmptyComposite::get();
        $this->children = new Map();
        $this->registerChildren();
    }

    public function __toString() : string
    {
        return 'Composite ' . $this->getName();
    }

    public function accept($visitor)
    {
        $methodToCall = 'accept' . $this->getClassShort();

        return $visitor->$methodToCall($this);
    }

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
     * @param bool $skipThis - пропустить этот узел?
     * @return \Generator
     */
    public function traverseBreadthFirst(bool $skipThis = false) : \Generator
    {
        $deque = new Deque();

        if (!$skipThis)
        {
            $deque->push($this);
        }
        else
        {
            $deque->push(... $this->getChildren()->values());
        }

        while (!$deque->isEmpty())
        {
            /** @var Composite $node */
            $node = $deque->shift();
            yield $node; // Вначале возвращаем элемент - может быть это WCollection и по ходу теста она подзагрузит себе детей
            $deque->push(... $node->getChildren()->values());
        }
    }

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
     * @param bool $skipThis - пропустить этот узел?
     * @return \Generator
     */
    public function traverseDepthFirst(bool $skipThis = false) : \Generator
    {
        $deque = new Deque();

        if (!$skipThis)
        {
            $deque->push($this);
        }
        else
        {
            $deque->push(... $this->getChildren()->values());
        }

        while (!$deque->isEmpty())
        {
            /** @var Composite $node */
            $node = $deque->shift();
            yield $node; // Вначале возвращаем элемент - может быть это WCollection и по ходу теста она подзагрузит себе детей
            $deque->unshift(... $node->getChildren()->values());
        }
    }

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
     * @param bool $skipThis - пропустить этот узел?
     * @return Sequence
     */
    public function traverseToRoot(bool $skipThis = false) : Sequence
    {
        $result = new Vector();

        if (!$skipThis)
        {
            $result->push($this);
        }

        $parent = $this->getParent();

        while (!$parent instanceof EmptyComposite)
        {
            $result->push($parent);
            $parent = $parent->getParent();
        }

        return $result;
    }

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
     * @param bool $skipThis - пропустить этот узел?
     * @return Sequence
     */
    public function traverseFromRoot(bool $skipThis = false) : Sequence
    {
        $result = new Deque();

        if (!$skipThis)
        {
            $result->push($this);
        }

        $parent = $this->getParent();

        while (!$parent instanceof EmptyComposite)
        {
            $result->unshift($parent);
            $parent = $parent->getParent();
        }

        return $result;
    }

    /**
     * Возвращает первого родителя узла с классом $class
     *
     * @param string $class
     * @throws UsageException
     */
    public function getFirstParentWithClass(string $class)
    {
        $parents = $this->traverseToRoot();
        $parents->shift();

        foreach ($parents as $parent)
        {
            if ($parent instanceof $class)
            {
                return $parent;
            }
        }

        throw new UsageException($this . ' -> не содержит родителя с классом: ' . $class);
    }
}
