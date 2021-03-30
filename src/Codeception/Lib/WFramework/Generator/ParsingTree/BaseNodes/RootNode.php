<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Ds\Map;
use Ds\Set;

class RootNode extends AbstractNode
{
    protected string $projectName;

    protected string $actorClassShort;

    protected string $actorClassFull;

    protected string $testNamespace;

    protected string $helperNamespace;

    protected string $generatedNamespace;

    /** @var Map|\ReflectionClass */
    protected Map $operationClassFullToReflectionClass;

    /** @var Set|string[] */
    protected Set $stepObjectClassesFull;



    public function __construct(string $projectName, string $projectActorClassFull, string $supportNamespace, string $testsNamespace, Map $operationClassFullToReflectionClass, Set $stepObjectClassesFull)
    {
        $this->projectName                          = $projectName;
        $this->actorClassShort                      = ClassHelper::getShortName($projectActorClassFull);
        $this->actorClassFull                       = $projectActorClassFull;
        $this->operationClassFullToReflectionClass  = $operationClassFullToReflectionClass;
        $this->stepObjectClassesFull                = $stepObjectClassesFull;
        $this->testNamespace                        = $testsNamespace;
        $this->helperNamespace                      = (!empty($supportNamespace) ? "$supportNamespace\\" : '') . 'Helper';
        $this->generatedNamespace                   = (!empty($supportNamespace) ? "$supportNamespace\\" : '') . '_generated';

        parent::__construct();
    }

    public function addPageObjectNode(string $name, string $baseClassFull) : PageObjectNode
    {
        $node = new PageObjectNode($name, $baseClassFull, $this);
        $this->addChild($node);
        return $node;
    }

    public function addStepsNode(string $name) : StepsNode
    {
        $node = new StepsNode($name, $this);
        $this->addChild($node);
        return $node;
    }

    public function addTestExampleNode(string $classShort, StepsNode $stepsNode) : TestExampleNode
    {
        $node = new TestExampleNode($classShort, $stepsNode, $this);
        $this->addChild($node);
        return $node;
    }


    public function getPageObjectNode(string $name) : PageObjectNode
    {
        /** @var PageObjectNode $po */
        $po = $this->getChildByName($name);
        return $po;
    }

    public function getStepsNode(string $name) : StepsNode
    {
        /** @var StepsNode $steps */
        $steps = $this->getChildByName($name);
        return $steps;
    }

    public function getProjectName() : string
    {
        return $this->projectName;
    }

    public function getActorClassFull() : string
    {
        return $this->actorClassFull;
    }

    public function getTestNamespace() : string
    {
        return $this->testNamespace;
    }

    public function getOperationClassFullToReflectionClass() : Map
    {
        return $this->operationClassFullToReflectionClass;
    }

    public function getStepObjectClassesFull() : Set
    {
        return $this->stepObjectClassesFull;
    }

    public function getActorClassShort() : string
    {
        return $this->actorClassShort;
    }

    public function getHelperNamespace() : string
    {
        return $this->helperNamespace;
    }

    public function getGeneratedNamespace() : string
    {
        return $this->generatedNamespace;
    }
}
