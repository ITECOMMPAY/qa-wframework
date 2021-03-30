<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;

class TestExampleNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;

    private StepsNode $stepsNode;



    public function __construct(string $entityClassShort, StepsNode $stepsNode, RootNode $parent)
    {
        $this->setParent($parent);

        $this->name              = $entityClassShort;
        $this->outputNamespace   = $this->getRootNode()->getTestNamespace();
        $this->entityClassShort  = $entityClassShort;
        $this->entityClassFull   = $this->outputNamespace . '\\' . $entityClassShort;
        $this->stepsNode         = $stepsNode;

        parent::__construct();

    }

    public function getRootNode() : RootNode
    {
        /** @var RootNode $node */
        $node = $this->getParent();
        return $node;
    }

    public function getStepsNode() : StepsNode
    {
        return $this->stepsNode;
    }

    public function getOutputNamespace() : string
    {
        return $this->outputNamespace;
    }

    public function getEntityClassShort() : string
    {
        return $this->entityClassShort;
    }

    public function getEntityClassFull() : string
    {
        return $this->entityClassFull;
    }
}