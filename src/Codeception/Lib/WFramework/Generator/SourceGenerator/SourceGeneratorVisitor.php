<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\FacadeNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\OperationGroupNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\OperationNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepsNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\TestExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block\LoginBlockNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Steps\LoginStepsNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Tests\SelfCheckCestNode;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\BlockSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\CollectionSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\ElementSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\FacadeSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\OperationGroupSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\OperationSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure\StepsSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Blocks\LoginBlockSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\ButtonSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\CheckboxSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\ImageSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\LabelSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\LinkSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Elements\TextBoxSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Steps\FrontPageStepsSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Steps\LoginStepsSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests\LoginCestSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests\SelfCheckCestSource;
use Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests\StoreShotsCestSource;
use Codeception\Lib\WFramework\Helpers\Composite;
use Codeception\Lib\WFramework\Helpers\CompositeVisitor;

class SourceGeneratorVisitor extends CompositeVisitor
{
    public function acceptComposite(Composite $composite){}

    public function acceptPageObjectNode(PageObjectNode $node)
    {
        if ($node->getName() === 'Block')
        {
            (new BlockSource($node))->generate(); return;
        }

        if ($node->getName() === 'Element')
        {
            (new ElementSource($node))->generate(); return;
        }

        if ($node->getName() === 'Collection')
        {
            (new CollectionSource($node))->generate(); return;
        }
    }

    public function acceptFacadeNode(FacadeNode $node)
    {
        (new FacadeSource($node))->generate();
    }

    public function acceptOperationGroupNode(OperationGroupNode $node)
    {
        /**
         * @var string $methodName
         * @var OperationNode $operationNode
         */
        foreach ($node->getChildren() as $methodName => $operationNode)
        {
            (new OperationSource($operationNode))->generate();
        }

        (new OperationGroupSource($node))->generate();
    }

    public function acceptStepsNode(StepsNode $node)
    {
        (new StepsSource($node))->generate();
    }

    public function acceptPageObjectExampleNode(PageObjectExampleNode $node)
    {
        if ($node->getName() === 'Button')
        {
            (new ButtonSource($node))->generate(); return;
        }

        if ($node->getName() === 'Checkbox')
        {
            (new CheckboxSource($node))->generate(); return;
        }

        if ($node->getName() === 'Image')
        {
            (new ImageSource($node))->generate(); return;
        }

        if ($node->getName() === 'Label')
        {
            (new LabelSource($node))->generate(); return;
        }

        if ($node->getName() === 'Link')
        {
            (new LinkSource($node))->generate(); return;
        }

        if ($node->getName() === 'TextBox')
        {
            (new TextBoxSource($node))->generate(); return;
        }

        if ($node instanceof LoginBlockNode)
        {
            (new LoginBlockSource($node))->generate(); return;
        }
    }

    public function acceptStepExampleNode(StepExampleNode $node)
    {
        if ($node->getName() === 'FrontPageSteps')
        {
            (new FrontPageStepsSource($node))->generate(); return;
        }

        if ($node instanceof LoginStepsNode)
        {
            (new LoginStepsSource($node))->generate(); return;
        }
    }

    public function acceptTestExampleNode(TestExampleNode $node)
    {
        if ($node->getName() === 'LoginCest')
        {
            (new LoginCestSource($node))->generate(); return;
        }

        if ($node->getName() === 'storeShotsCest')
        {
            (new StoreShotsCestSource($node))->generate(); return;
        }

        if ($node instanceof SelfCheckCestNode)
        {
            (new SelfCheckCestSource($node))->generate(); return;
        }
    }
}
