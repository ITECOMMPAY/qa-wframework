<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes;


use Codeception\Lib\WFramework\Helpers\Composite;

abstract class AbstractOperationGroupNode extends Composite
{
    public $name;

    public $classFull;

    public $outputNamespace;

    public $source = null;

    public $operationNameToAcceptMethod = [];

    public function __construct(string $name, string $outputNamespace)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
    }

    /**
     * @param AbstractOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        $this->addChild($operationNode);
    }

    /**
     * @return AbstractFacadeNode
     */
    public function getFacade()
    {
        /** @var AbstractFacadeNode $parent */
        $parent = $this->getParent();
        return $parent;
    }
}
