<?php


namespace Common\Module\WFramework\Generator\ParsingTree;


use Common\Module\WFramework\Generator\ParsingTree\Block\BlockFacadeNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockOperationNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementFacadeNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementOperationNode;
use Common\Module\WFramework\Helpers\Composite;

class RootNode extends Composite
{
    public $name;

    public $projectActorClassFull;

    public $outputNamespace;

    public $operationClassFullToReflectionClass;

    public $source = null;

    public function __construct(string $projectName, string $projectActorClassFull, string $outputNamespace, array $operationClassFullToReflectionClass)
    {
        parent::__construct();

        $this->name = $projectName;
        $this->projectActorClassFull = $projectActorClassFull;
        $this->outputNamespace = $outputNamespace;
        $this->operationClassFullToReflectionClass = $operationClassFullToReflectionClass;

        $this->buildTree();
    }

    protected function buildTree()
    {
        $helperNamespace = $this->outputNamespace . '\Helper';
        $generatedOperationsNamespace = $this->outputNamespace . '\_generated';

        $blockNode = new BlockNode($this->name . 'Block', $helperNamespace . '\Blocks', $this->projectActorClassFull);
        $this->addChild($blockNode);

        $blockFacadeNode = new BlockFacadeNode('Operations', $generatedOperationsNamespace . '\Block');
        $blockNode->addFacade($blockFacadeNode);

        $elementNode = new ElementNode($this->name . 'Element', $helperNamespace . '\Elements', $this->projectActorClassFull);
        $this->addChild($elementNode);

        $elementFacadeNode = new ElementFacadeNode('Operations', $generatedOperationsNamespace . '\Element');
        $elementNode->addFacade($elementFacadeNode);

        foreach ($this->operationClassFullToReflectionClass as $operationClassFull => $reflectionClass)
        {
            $blockOperationNode = BlockOperationNode::tryFrom($operationClassFull, $reflectionClass);

            if ($blockOperationNode !== null)
            {
                $blockFacadeNode->addOperation($blockOperationNode);
            }

            $elementOperationNode = ElementOperationNode::tryFrom($operationClassFull, $reflectionClass);

            if ($elementOperationNode !== null)
            {
                $elementFacadeNode->addOperation($elementOperationNode);
            }
        }
    }
}
