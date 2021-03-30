<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;

class StepsNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;

    public function __construct(string $name, RootNode $parent)
    {
        $this->setParent($parent);

        $this->name             = $name;
        $this->outputNamespace  = $this->getRootNode()->getHelperNamespace() . '\Steps';
        $this->entityClassShort = $this->getRootNode()->getProjectName() . $name;
        $this->entityClassFull  = $this->outputNamespace . '\\' . $this->entityClassShort;

        parent::__construct();
    }

    public function addExampleNode(string $name, string $classShort) : StepExampleNode
    {
        $stepNode = new StepExampleNode($name, $classShort, $this);
        $this->addChild($stepNode);
        return $stepNode;
    }

    public function addExampleNodeExisting(StepExampleNode $node) : StepExampleNode
    {
        $this->addChild($node);
        return $node;
    }



    public function getRootNode() : RootNode
    {
        /** @var RootNode $node */
        $node = $this->getParent();
        return $node;
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