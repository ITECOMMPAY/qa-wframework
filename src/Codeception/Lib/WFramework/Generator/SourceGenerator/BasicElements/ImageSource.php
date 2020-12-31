<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
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

    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $imageClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $imageClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->imageClassFull = $imageClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('image_class_short', ClassHelper::getShortName($this->imageClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce();
    }
}