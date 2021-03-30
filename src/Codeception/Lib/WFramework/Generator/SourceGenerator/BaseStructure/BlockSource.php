<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectNode;
use Codeception\Util\Template;

class BlockSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_block_reference}};
use {{actor_class_full}};
use {{block_facade_class_full}};

abstract class {{block_class_short}} extends WBlock
{
    public function __construct({{actor_class_short}} $actor)
    {
        parent::__construct($actor);
    }
    
    public function returnCodeceptionActor() : {{actor_class_short}}
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnOperations() : {{block_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{block_facade_class_short}}($this);
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
                        ->place('namespace',                $this->node->getOutputNamespace())
                        ->place('w_block_reference',        $this->node->getBasePageObjectClassFull())
                        ->place('block_class_short',        $this->node->getEntityClassShort())
                        ->place('actor_class_full',         $this->node->getRootNode()->getActorClassFull())
                        ->place('actor_class_short',        $this->node->getRootNode()->getActorClassShort())
                        ->place('block_facade_class_full',  $this->node->getFacadeNode()->getEntityClassFull())
                        ->place('block_facade_class_short', $this->node->getFacadeNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}
