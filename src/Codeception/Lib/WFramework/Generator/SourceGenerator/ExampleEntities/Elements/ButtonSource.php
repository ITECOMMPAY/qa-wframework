<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Util\Template;

class ButtonSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IClickable;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use Codeception\Lib\WFramework\Logger\WLogger;
use {{element_class_full}};

class {{button_class_short}} extends {{element_class_short}} implements IClickable, IHaveReadableText
{
    protected function initTypeName() : string
    {
        return 'Кнопка';
    }

    public function click() : {{button_class_short}}
    {
        WLogger::logAction($this, "кликаем");
    
        $this
            ->returnOperations()
            ->mouse()
            ->clickSmart()
            ;

        return $this;
    }

    public function clickMouseDown() : {{button_class_short}}
    {
        WLogger::logAction($this, "кликаем (Mouse Down)");
    
        $this
            ->returnOperations()
            ->mouse()
            ->clickWithLeftButton()
            ;

        return $this;
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
                        ->place('button_class_short',   $this->node->getEntityClassShort())
                        ->place('element_class_full',   $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('element_class_short',  $this->node->getPageObjectNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}