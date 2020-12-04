<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Codeception\Util\Template;

class OperationsParentSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{abstract_operation_reference}}
use {{block_class_full}};
use {{element_class_full}};
use {{collection_class_full}};

abstract class {{operations_parent_class_short}} extends AbstractOperation
{
    /**
     * @param {{block_class_short}} $block
     * @return mixed|void
     */
    public function acceptWBlock($block)
    {
        return parent::acceptWBlock($block);
    }

    /**
     * @param {{element_class_short}} $element
     * @return mixed|void
     */
    public function acceptWElement($element)
    {
        return parent::acceptWElement($element);
    }

    /**
     * @param {{collection_class_short}} $collection
     * @return mixed|void
     */
    public function acceptWCollection($collection)
    {
        return parent::acceptWCollection($collection);
    }
}

EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $operationsParentClassShort;

    /** @var string */
    protected $blockClassFull;

    /** @var string */
    protected $elementClassFull;

    /** @var string */
    protected $collectionClassFull;


    public function __construct(string $namespace, string $operationsParentClassShort, string $blockClassFull, string $elementClassFull, string $collectionClassFull)
    {
        $this->namespace = $namespace;
        $this->operationsParentClassShort = $operationsParentClassShort;
        $this->blockClassFull = $blockClassFull;
        $this->elementClassFull = $elementClassFull;
        $this->collectionClassFull = $collectionClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('abstract_operation_reference', AbstractOperation::class)
                        ->place('block_class_full', $this->blockClassFull)
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('collection_class_full', $this->collectionClassFull)
                        ->place('operations_parent_class_short', $this->operationsParentClassShort)
                        ->place('block_class_short', ClassHelper::getShortName($this->blockClassFull))
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->place('collection_class_short', ClassHelper::getShortName($this->collectionClassFull))
                        ->produce();
    }
}
