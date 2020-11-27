<?php


namespace Common\Module\WFramework\Generator\SourceGenerator;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractFacadeNode;
use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationGroupNode;
use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockFacadeNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockOperationGroupNode;
use Common\Module\WFramework\Generator\ParsingTree\Block\BlockOperationNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementFacadeNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementOperationGroupNode;
use Common\Module\WFramework\Generator\ParsingTree\Element\ElementOperationNode;
use Common\Module\WFramework\Generator\ParsingTree\RootNode;

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

    public function acceptBlockFacadeNode(BlockFacadeNode $node)
    {
        $this->acceptFacadeNode($node);
    }

    public function acceptElementFacadeNode(ElementFacadeNode $node)
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

    protected function acceptOperationNode(AbstractOperationNode $node)
    {
        if ($node->source !== null)
        {
            return;
        }

        $node->source = (new OperationSource($node->name, $node->classFull, $node->reflectionClass, $node->reflectionMethod))->produce();
    }
}
