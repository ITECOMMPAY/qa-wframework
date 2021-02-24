<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Util\Template;

class LinkSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Logger\WLogger;
use {{element_class_full}};

class {{link_class_short}} extends {{element_class_short}}
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
}
EOF;

    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $linkClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $linkClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->linkClassFull = $linkClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('link_class_short', ClassHelper::getShortName($this->linkClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce();
    }
}