<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 16:05
 */

namespace Common\Module\WFramework\WebObjects\Base;


use Common\Module\WFramework\Helpers\ModernObject;
use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\WebObjects\Base\EmptyObjects\EmptyWObject;
use function array_keys;
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
abstract class WObject extends ModernObject
{
    /** @var WObject */
    private $parentWObject = null;

    /** @var WObject[] */
    private $childrenWObjects = [];

    /** @var string  */
    protected $name = '';

    /**
     * Возвращает своё имя.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Задаёт элемент, как своего родителя
     *
     * @param WObject $parentWObject
     */
    protected function setParent(WObject $parentWObject)
    {
        $this->parentWObject = $parentWObject;
    }

    /**
     * Возвращает своего родителя
     *
     * Если родителя нет, то возвращает EmptyWObject
     *
     * @return WObject|WPageObject
     */
    public function getParent()
    {
        return $this->parentWObject;
    }

    /**
     * Добавляет все прописанные в данном классе WObject себе в дети
     */
    private function registerChildren()
    {
        foreach ($this as $fieldName => $fieldValue)
        {
            if ($fieldName !== 'parentWObject' && ($fieldValue instanceof WObject))
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
     * @param WObject $child
     */
    protected function addChild(WObject $child)
    {
        $this->childrenWObjects[$child->getName()] = $child;
        $child->setParent($this);
    }

    /**
     * Очищает массив своих детей
     */
    protected function clearChildren()
    {
        $this->childrenWObjects = [];
    }

    /**
     * Возвращает ассоциативный массив детей
     *
     * Массив имеет вид: имя WObject => WObject
     *
     * @return WObject[]|WPageObject[]
     */
    public function getChildren() : array
    {
        return $this->childrenWObjects;
    }

    /**
     * Возвращает своего ребёнка по его имени
     *
     * @param string $name - имя
     * @return WObject|WPageObject
     * @throws UsageException
     */
    public function getChildByName(string $name)
    {
        if (!isset($this->childrenWObjects[$name]))
        {
            throw new UsageException("В WObject'е нет элемента с именем: $name, есть только: " . implode(', ', array_keys($this->childrenWObjects)));
        }

        return $this->childrenWObjects[$name];
    }

    public function __construct()
    {
        $this->registerChildren();
        $this->parentWObject = EmptyWObject::get();
    }

    public function __toString() : string
    {
        return 'WObject ' . $this->getName();
    }
}
