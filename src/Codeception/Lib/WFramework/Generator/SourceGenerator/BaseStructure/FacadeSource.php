<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\FacadeNode;
use Codeception\Util\Template;

class FacadeSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{page_object_class_full}};
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;

class {{facade_class_short}}
{
    /**
     * Этот файл генерируется автоматически при запуске тестов или при вызове команды:
     * ./vendor/bin/codecept WBuild -c путь_к_codeception.yml
     * 
     * Править его вручную - не имеет смысла.
     */
    
    /** @var {{page_object_class_short}} */
    protected $pageObject;

    public function __construct({{page_object_class_short}} $pageObject)
    {
        $this->pageObject = $pageObject;
    }
    
    public function accept(PageObjectVisitor $operation)
    {
        return $this->pageObject->accept($operation);
    }

    {{groups}}
}
EOF;

    protected const GROUP_GETTER_TEMPLATE = <<<'EOF'

    public function {{group_name}}() : \{{group_class_full}}
    {
        return $this->{{group_name}} ?? $this->{{group_name}} = new \{{group_class_full}}($this);
    }
EOF;

    protected FacadeNode $node;

    public function __construct(FacadeNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $groups = [];

        foreach ($this->node->getFacadeOperationGroupNodes() as $groupName => $operationGroupNode)
        {
            $groups[] = (new Template(static::GROUP_GETTER_TEMPLATE))
                                ->place('group_name', lcfirst($groupName))
                                ->place('group_class_full', $operationGroupNode->getEntityClassFull())
                                ->produce();
        }

        $source = (new Template(static::TEMPLATE))
                        ->place('namespace', $this->node->getOutputNamespace())
                        ->place('facade_class_short', $this->node->getEntityClassShort())
                        ->place('page_object_class_full', $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('page_object_class_short', $this->node->getPageObjectNode()->getEntityClassShort())
                        ->place('groups', implode(PHP_EOL, $groups))
                        ->produce();

        $this->node->setSource($source);
    }
}
