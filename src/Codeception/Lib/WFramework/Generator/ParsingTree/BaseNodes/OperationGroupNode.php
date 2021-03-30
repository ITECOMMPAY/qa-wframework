<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;
use Ds\Map;

class OperationGroupNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;



    public function __construct(string $entityClassShort, FacadeNode $parent)
    {
        $this->setParent($parent);

        $this->name             = $entityClassShort;
        $this->outputNamespace  = $this->getFacadeNode()->getOutputNamespace();
        $this->entityClassShort = $entityClassShort;
        $this->entityClassFull  = $this->outputNamespace . '\\' . $entityClassShort;

        parent::__construct();
    }

    public function addOperation(OperationNode $operationNode) : void
    {
        $this->addChild($operationNode);
    }


    public function getFacadeNode() : FacadeNode
    {
        /** @var FacadeNode $facadeNode */
        $facadeNode = $this->getParent();

        return $facadeNode;
    }

    /**
     * @return Map|OperationNode[]
     */
    public function getOperationNodes() : Map
    {
        return $this->getChildren();
    }

    public function getEntityClassShort() : string
    {
        return $this->entityClassShort;
    }

    public function getEntityClassFull() : string
    {
        return $this->entityClassFull;
    }

    public function getOutputNamespace() : string
    {
        return $this->outputNamespace;
    }
}
