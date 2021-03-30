<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;

class StepExampleNode extends AbstractNode implements IDescribeClass
{
    protected string $outputNamespace;

    protected string $entityClassShort;

    protected string $entityClassFull;



    public function __construct(string $name, string $entityClassShort, StepsNode $parent)
    {
        $this->setParent($parent);

        $this->outputNamespace  = $this->getStepsNode()->getOutputNamespace();
        $this->name             = $name;
        $this->entityClassShort = $entityClassShort;
        $this->entityClassFull  = $this->outputNamespace . '\\' . $this->entityClassShort;

        $this->getStepsNode()->getRootNode()->getStepObjectClassesFull()->add($this->entityClassFull);

        parent::__construct();

    }




    public function getStepsNode() : StepsNode
    {
        /** @var StepsNode $node */
        $node = $this->getParent();
        return $node;
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