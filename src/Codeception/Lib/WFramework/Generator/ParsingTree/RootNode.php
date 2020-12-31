<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree;


use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\ButtonNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\CheckboxNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\ImageNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\LabelNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\LinkNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BasicElements\TextBoxNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Block\BlockOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Collection\CollectionOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementFacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementOperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\OperationTemplate\OperationTemplateNode;
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
            $blockOperationNode = BlockOperationNode::tryCreateFrom($operationClassFull, $reflectionClass, $blockNode->getVisitorNames());

            if ($blockOperationNode !== null)
            {
                $blockFacadeNode->addOperation($blockOperationNode);
            }

            $elementOperationNode = ElementOperationNode::tryCreateFrom($operationClassFull, $reflectionClass, $elementNode->getVisitorNames());

            if ($elementOperationNode !== null)
            {
                $elementFacadeNode->addOperation($elementOperationNode);
            }

            $collectionOperationNode = CollectionOperationNode::tryCreateFrom($operationClassFull, $reflectionClass, $collectionNode->getVisitorNames());

            if ($collectionOperationNode !== null)
            {
                $collectionFacadeNode->addOperation($collectionOperationNode);
            }
        }

        $buttonNode = new ButtonNode(
            $this->name . 'Button',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($buttonNode);

        $checkboxNode = new CheckboxNode(
            $this->name . 'Checkbox',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($checkboxNode);

        $linkNode = new LinkNode(
            $this->name . 'Link',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($linkNode);

        $imageNode = new ImageNode(
            $this->name . 'Image',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($imageNode);

        $labelNode = new LabelNode(
            $this->name . 'Label',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($labelNode);

        $textBoxNode = new TextBoxNode(
            $this->name . 'TextBox',
            $helperNamespace . '\Elements\Basic'
        );
        $this->addChild($textBoxNode);
    }
}
