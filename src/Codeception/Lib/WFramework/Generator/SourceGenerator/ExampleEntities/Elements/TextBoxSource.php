<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Util\Template;

class TextBoxSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Conditions\Not_;
use Codeception\Lib\WFramework\Conditions\TextEmpty;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveWritableText;
use Codeception\Lib\WFramework\Logger\WLogger;
use {{element_class_full}};

class {{text_box_class_short}} extends {{element_class_short}} implements IHaveWritableText, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Поле ввода';
    }

    public function set(string $text) : {{text_box_class_short}}
    {
        WLogger::logAction($this, "задаём текст: $text");
    
        $this
            ->returnOperations()
            ->field()
            ->set($text, 0)
            ;

        return $this;
    }

    public function append(string $text) : {{text_box_class_short}}
    {
        WLogger::logAction($this, "дописываем в конец: $text");
    
        $this
            ->returnOperations()
            ->field()
            ->append($text, 0)
            ;

        return $this;
    }

    public function prepend(string $text) : {{text_box_class_short}}
    {
        WLogger::logAction($this, "дописываем в начало: $text");
    
        $this
            ->returnOperations()
            ->field()
            ->prepend($text, 0)
            ;

        return $this;
    }

    public function clear() : {{text_box_class_short}}
    {
        WLogger::logAction($this, "очищаем");
    
        $this
            ->returnOperations()
            ->field()
            ->clear(0)
            ;

        return $this;
    }

    public function isEmpty() : bool
    {
        return $this->is(new TextEmpty());
    }

    public function isNotEmpty() : bool
    {
        return $this->is(new Not_(new TextEmpty()));
    }

    public function shouldBeEmpty() : {{text_box_class_short}}
    {
        return $this->should(new TextEmpty());
    }

    public function shouldNotBeEmpty() : {{text_box_class_short}}
    {
        return $this->should(new Not_(new TextEmpty()));
    }

    public function finallyEmpty() : bool
    {
        return $this->finally_(new TextEmpty());
    }

    public function finallyNotEmpty() : bool
    {
        return $this->finally_(new Not_(new TextEmpty()));
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
                        ->place('text_box_class_short', $this->node->getEntityClassShort())
                        ->place('element_class_full',   $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('element_class_short',  $this->node->getPageObjectNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}