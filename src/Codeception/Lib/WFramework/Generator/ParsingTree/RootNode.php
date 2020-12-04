<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree;


use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\OperationsParent\OperationsParentNode;
use Codeception\Lib\WFramework\Helpers\Composite;

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

        $collectionNode = new CollectionNode($this->name . 'Collection', $helperNamespace . '\Collections', $this->projectActorClassFull);
        $this->addChild($collectionNode);

        $collectionFacadeNode = new CollectionFacadeNode('Operations', $generatedOperationsNamespace . '\Collection');
        $collectionNode->addFacade($collectionFacadeNode);

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

            $collectionOperationNode = CollectionOperationNode::tryFrom($operationClassFull, $reflectionClass);

            if ($collectionOperationNode !== null)
            {
                $collectionFacadeNode->addOperation($collectionOperationNode);
            }
        }

        $operationsAbstractParent = new OperationsParentNode(
                                        $this->name . 'Operation',
                                        $helperNamespace . '\Operations',
                                        $blockNode->classFull,
                                        $elementNode->classFull,
                                        $collectionNode->classFull
        );
        $this->addChild($operationsAbstractParent);
    }
}
