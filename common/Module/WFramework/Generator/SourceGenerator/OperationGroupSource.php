<?php


namespace Common\Module\WFramework\Generator\SourceGenerator;


use Codeception\Lib\Generator\Shared\Classname;
use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;
use Common\Module\WFramework\Generator\IGenerator;
use Common\Module\WFramework\Helpers\ClassHelper;

class OperationGroupSource implements IGenerator
{
    use Classname;
    use Namespaces;

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{facade_class_full}};

class {{group_class_short}}
{
    /** @var {{facade_class_short}} */
    protected $facade;

    public function __construct({{facade_class_short}} $facade)
    {
        $this->facade = $facade;
    }
    
    protected function getFacade() : {{facade_class_short}}
    {
        return $this->facade;
    }
    
    public function then() : {{facade_class_short}}
    {
        return $this->getFacade();
    }

    {{operations}}
}
EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $groupClassShort;

    /** @var string */
    protected $facadeClassFull;

    /** @var string */
    protected $pageObjectClassFull;

    /** @var string[] */
    protected $operationNameToClass;

    /** @var string */
    protected $operationsSource;


    public function __construct(string $namespace, string $groupClassShort, string $facadeClassFull, string $operationsSource)
    {
        $this->namespace = $namespace;
        $this->groupClassShort = $groupClassShort;
        $this->facadeClassFull = $facadeClassFull;
        $this->operationsSource = $operationsSource;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('group_class_short', $this->groupClassShort)
                        ->place('facade_class_full', $this->facadeClassFull)
                        ->place('facade_class_short', ClassHelper::getShortName($this->facadeClassFull))
                        ->place('operations', $this->operationsSource)
                        ->produce();
    }
}
