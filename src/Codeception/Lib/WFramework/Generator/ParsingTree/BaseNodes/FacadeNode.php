<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;
use Ds\Map;

class FacadeNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;



    public function __construct(string $entityClassShort, PageObjectNode $parent)
    {
        $this->setParent($parent);

        $this->name              = $entityClassShort;
        $this->outputNamespace   = $this->getPageObjectNode()->getRootNode()->getGeneratedNamespace() . '\\' . $this->getPageObjectNode()->getName();
        $this->entityClassShort  = $entityClassShort;
        $this->entityClassFull   = $this->outputNamespace . '\\' . $entityClassShort;

        parent::__construct();
    }

    protected function getVisitorNames() : array
    {
        return ['accept' . $this->getPageObjectNode()->getEntityClassShort(), 'accept' . $this->getPageObjectNode()->getBasePageObjectClassShort()];
    }

    public function addOperations() : void
    {
        $map = $this->getPageObjectNode()->getRootNode()->getOperationClassFullToReflectionClass();

        foreach ($map as $operationClassFull => $reflectionClass)
        {
            $operationNode = OperationNode::tryCreateFrom($operationClassFull, $reflectionClass, $this->getVisitorNames());

            if ($operationNode === null)
            {
                continue;
            }

            $this->addOperation($operationNode);
        }
    }

    protected function addOperation(OperationNode $operationNode) : void
    {
        $group = $this->getNewOperationGroup($operationNode->getGroupName());
        $group->addOperation($operationNode);
    }

    protected function getNewOperationGroup(string $groupName) : OperationGroupNode
    {
        if (!$this->hasChild($groupName))
        {
            $group = new OperationGroupNode($groupName, $this);
            $this->addChild($group);
        }

        /** @var OperationGroupNode $facadeOperationGroupNode */
        $facadeOperationGroupNode = $this->getChildByName($groupName);

        return $facadeOperationGroupNode;
    }


    public function getPageObjectNode() : PageObjectNode
    {
        /** @var PageObjectNode $pageObjectNode */
        $pageObjectNode = $this->getParent();

        return $pageObjectNode;
    }

    /**
     * @return Map|OperationGroupNode[]
     */
    public function getFacadeOperationGroupNodes() : Map
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
