<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\OperationGroupNode;
use Codeception\Util\Template;

class OperationGroupSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{facade_class_full}};

class {{group_class_short}}
{
   /**
    * Этот файл генерируется автоматически при запуске тестов или при вызове команды:
    * ./vendor/bin/codecept WBuild -c путь_к_codeception.yml
    * 
    * Править его вручную - не имеет смысла.
    */

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

    protected OperationGroupNode $node;

    public function __construct(OperationGroupNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $operationsSource = '';

        foreach ($this->node->getOperationNodes() as $methodName => $operationNode)
        {
            if ($operationNode->getSource() === null)
            {
                throw new UsageException('Сначала нужно сгенерировать операции');
            }

            $operationsSource .= PHP_EOL . $operationNode->getSource();
        }

        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('group_class_short',    $this->node->getEntityClassShort())
                        ->place('facade_class_full',    $this->node->getFacadeNode()->getEntityClassFull())
                        ->place('facade_class_short',   $this->node->getFacadeNode()->getEntityClassShort())
                        ->place('operations',           $operationsSource)
                        ->produce();

        $this->node->setSource($source);
    }
}
