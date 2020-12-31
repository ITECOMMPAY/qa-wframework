<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Util\Template;

class ButtonSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IClickable;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use {{element_class_full}};

class {{button_class_short}} extends {{element_class_short}} implements IClickable, IHaveReadableText
{
    protected function initTypeName() : string
    {
        return 'Кнопка';
    }

    public function click() : {{button_class_short}}
    {
        $this
            ->returnOperations()
            ->mouse()
            ->clickSmart()
            ;

        return $this;
    }

    public function clickMouseDown() : {{button_class_short}}
    {
        $this
            ->returnOperations()
            ->mouse()
            ->clickWithLeftButton()
            ;

        return $this;
    }

    public function getFilteredText(string $regex) : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex)
                    ;
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
    protected $buttonClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $buttonClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->buttonClassFull = $buttonClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('button_class_short', ClassHelper::getShortName($this->buttonClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce();
    }
}