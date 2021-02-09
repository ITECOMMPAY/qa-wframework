<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
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

    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $textBoxClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $textBoxClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->textBoxClassFull = $textBoxClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('text_box_class_short', ClassHelper::getShortName($this->textBoxClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce();
    }
}