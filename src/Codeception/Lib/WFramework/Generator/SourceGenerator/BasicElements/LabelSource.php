<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Util\Template;

class LabelSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use {{element_class_full}};

class {{label_class_short}} extends {{element_class_short}} implements IHaveReadableText, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Текстовый элемент';
    }

    public function getFilteredText(string $regex) : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex)
                    ;
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
    protected $labelClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $labelClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->labelClassFull = $labelClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('label_class_short', ClassHelper::getShortName($this->labelClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce()
                        ;
    }
}