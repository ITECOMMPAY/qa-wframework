<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationGroupNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockOperationGroupNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionOperationGroupNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementOperationGroupNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\OperationsParent\OperationsParentNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\RootNode;

class SourceGeneratorVisitor
{
    public function acceptRootNode(RootNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = '';
    }

    public function acceptBlockNode(BlockNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new BlockSource($node->outputNamespace, $node->name, $node->actorClassFull, $node->getFacade()->classFull))->produce();
    }

    public function acceptElementNode(ElementNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new ElementSource($node->outputNamespace, $node->name, $node->actorClassFull, $node->getFacade()->classFull))->produce();
    }

    public function acceptCollectionNode(CollectionNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new CollectionSource($node->outputNamespace, $node->name, $node->actorClassFull, $node->getFacade()->classFull))->produce();
    }

    public function acceptBlockFacadeNode(BlockFacadeNode $node)
    {
        $this->acceptFacadeNode($node);
    }

    public function acceptElementFacadeNode(ElementFacadeNode $node)
    {
        $this->acceptFacadeNode($node);
    }

    public function acceptCollectionFacadeNode(CollectionFacadeNode $node)
    {
        $this->acceptFacadeNode($node);
    }

    protected function acceptFacadeNode(AbstractFacadeNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $groupNameToGroupClassFull = [];

        /**
         * @var string $name
         * @var AbstractOperationGroupNode $child
         */
        foreach ($node->getChildren() as $name => $child)
        {
            $groupNameToGroupClassFull[$name] = $child->classFull;
        }

        $node->source = (new FacadeSource($node->outputNamespace, $node->name, $node->getPageObject()->classFull, $groupNameToGroupClassFull))->produce();
    }

    public function acceptBlockOperationGroupNode(BlockOperationGroupNode $node)
    {
        $this->acceptOperationGroupNode($node);
    }

    public function acceptElementOperationGroupNode(ElementOperationGroupNode $node)
    {
        $this->acceptOperationGroupNode($node);
    }

    public function acceptCollectionOperationGroupNode(CollectionOperationGroupNode $node)
    {
        $this->acceptOperationGroupNode($node);
    }

    protected function acceptOperationGroupNode(AbstractOperationGroupNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $operationsSources = [];

        /**
         * @var string $name
         * @var AbstractOperationNode $operation
         */
        foreach ($node->getChildren() as $name => $operation)
        {
            $operation->accept($this);
            $operationsSources[] = $operation->source;
        }

        $operationsSource = implode(PHP_EOL, $operationsSources);

        $node->source = (new OperationGroupSource($node->outputNamespace, $node->name, $node->getFacade()->classFull, $operationsSource))->produce();
    }

    public function acceptBlockOperationNode(BlockOperationNode $node)
    {
        $this->acceptOperationNode($node);
    }

    public function acceptElementOperationNode(ElementOperationNode $node)
    {
        $this->acceptOperationNode($node);
    }

    public function acceptCollectionOperationNode(CollectionOperationNode $node)
    {
        $this->acceptOperationNode($node);
    }

    protected function acceptOperationNode(AbstractOperationNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new OperationSource($node->name, $node->classFull, $node->reflectionClass, $node->reflectionMethod))->produce();
    }

    public function acceptOperationsParentNode(OperationsParentNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new OperationsParentSource($node->outputNamespace, $node->name, $node->blockClassFull, $node->elementClassFull, $node->collectionClassFull))->produce();
    }

}
