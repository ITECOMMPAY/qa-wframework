<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Util\Template;

class ImageSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{element_class_full}};

class {{image_class_short}} extends {{element_class_short}}
{
    protected function initTypeName() : string
    {
        return 'Картинка';
    }
}
EOF;

    protected PageObjectExampleNode $node;

    public function __construct(PageObjectExampleNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('image_class_short',    $this->node->getEntityClassShort())
                        ->place('element_class_full',   $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('element_class_short',  $this->node->getPageObjectNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}