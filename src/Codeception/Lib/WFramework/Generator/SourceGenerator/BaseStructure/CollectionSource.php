<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectNode;
use Codeception\Util\Template;

class CollectionSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_collection_reference}};
use {{actor_class_full}};
use {{collection_facade_class_full}};

class {{collection_class_short}} extends WCollection
{
    public function returnOperations() : {{collection_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{collection_facade_class_short}}($this);
    }
}
EOF;

    protected PageObjectNode $node;

    public function __construct(PageObjectNode $node)
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
            ->produce();

        $this->node->setSource($source);
    }
}
