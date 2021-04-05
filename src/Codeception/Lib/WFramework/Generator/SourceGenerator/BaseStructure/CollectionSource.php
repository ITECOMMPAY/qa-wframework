<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\CollectionNode;
use Codeception\Util\Template;

class CollectionSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_collection_reference}};
use {{actor_class_full}};
use {{collection_facade_class_full}};
use {{element_class_full}};
use Ds\Sequence;

class {{collection_class_short}} extends WCollection
{
    public function returnOperations() : {{collection_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{collection_facade_class_short}}($this);
    }
    
    //Ниже переопределяем методы Коллекции, чтобы они возвращали классы нашего проекта
    
    /**
     * Данный метод возвращает массив элементов коллекции
     *
     * Элементы коллекции будут иметь тот же класс, что и веб-элемент из которого она была создана
     *
     * @return {{element_class_short}}[]|Sequence
     */
    public function getElementsArray() : Sequence
    {
        return parent::getElementsArray();
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
     * @return {{element_class_short}}[]
     */
    public function getElementsMap(string $methodOrProperty, bool $preserveDuplicates = false) : array
    {
        return parent::getElementsMap($methodOrProperty, $preserveDuplicates);
    }

    public function getFirstElement() : {{element_class_short}}
    {
        /** @var {{element_class_short}} $element */
        $element = parent::getFirstElement();
        return $element;
    }

    public function getElement(int $index) : {{element_class_short}}
    {
        /** @var {{element_class_short}} $element */
        $element = parent::getElement($index);
        return $element;
    }

    public function getLastElement() : {{element_class_short}}
    {
        /** @var {{element_class_short}} $element */
        $element = parent::getLastElement();
        return $element;
    }
    
        public function shouldExist(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldExist($deep);
    }
    
    public function shouldNotExist(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldNotExist($deep);
    }
    
    public function shouldBeDisplayed(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeDisplayed($deep);
    }
    
    public function shouldBeHidden(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeHidden($deep);
    }
    
    public function shouldBeEnabled(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeEnabled($deep);
    }
    
    public function shouldBeDisabled(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeDisabled($deep);
    }
    
    public function shouldBeInViewport(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeInViewport($deep);
    }
    
    public function shouldBeOutOfViewport(bool $deep = true) : {{collection_class_short}}
    {
        return parent::shouldBeOutOfViewport($deep);
    }
}
EOF;

    protected CollectionNode $node;

    public function __construct(CollectionNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
            ->place('namespace',                        $this->node->getOutputNamespace())
            ->place('w_collection_reference',           $this->node->getBasePageObjectClassFull())
            ->place('collection_class_short',           $this->node->getEntityClassShort())
            ->place('actor_class_full',                 $this->node->getRootNode()->getActorClassFull())
            ->place('actor_class_short',                $this->node->getRootNode()->getActorClassShort())
            ->place('collection_facade_class_full',     $this->node->getFacadeNode()->getEntityClassFull())
            ->place('collection_facade_class_short',    $this->node->getFacadeNode()->getEntityClassShort())
            ->place('element_class_full',               $this->node->getProjectElement()->getEntityClassFull())
            ->place('element_class_short',              $this->node->getProjectElement()->getEntityClassShort())
            ->produce();

        $this->node->setSource($source);
    }
}
