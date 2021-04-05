<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Util\Template;

class LinkSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use {{element_class_full}};

class {{link_class_short}} extends {{element_class_short}} implements IHaveReadableText
{
    protected function initTypeName() : string
    {
        return 'Ссылка на файл';
    }

    public function download() : string
    {
        WLogger::logAction($this, "скачиваем файл");
    
        return $this
                    ->returnOperations()
                    ->get()
                    ->file()
                    ;
    }
    
    
    public function getFilteredText(string $regex, string $groupName = "") : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex, $groupName)
                    ;
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
                        ->place('link_class_short',     $this->node->getEntityClassShort())
                        ->place('element_class_full',   $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('element_class_short',  $this->node->getPageObjectNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}