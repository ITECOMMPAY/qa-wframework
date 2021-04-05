<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Util\Template;

class LabelSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use Codeception\Lib\WFramework\Logger\WLogger;
use {{element_class_full}};

class {{label_class_short}} extends {{element_class_short}} implements IHaveReadableText, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Текстовый элемент';
    }

    public function getFilteredText(string $regex, string $groupName = "") : string
    {
        WLogger::logAction($this, "получаем надпись отфильтрованную по регулярке: $regex");
    
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex, $groupName)
                    ;
    }

    public function getCurrentValueString() : string
    {
        return $this->getAllText();
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
                        ->place('label_class_short',    $this->node->getEntityClassShort())
                        ->place('element_class_full',   $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('element_class_short',  $this->node->getPageObjectNode()->getEntityClassShort())
                        ->produce()
                        ;

        $this->node->setSource($source);
    }
}