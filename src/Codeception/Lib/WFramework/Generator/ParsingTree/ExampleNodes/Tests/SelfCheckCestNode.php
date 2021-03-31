<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Tests;


use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\RootNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepsNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\TestExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block\LoginBlockNode;

class SelfCheckCestNode extends TestExampleNode
{
    private LoginBlockNode $loginBlockNode;

    public function __construct(
        string $entityClassShort,
        StepsNode $stepsNode,
        RootNode $parent,
        LoginBlockNode $loginBlockNode
    )
    {
        $this->loginBlockNode = $loginBlockNode;

        parent::__construct($entityClassShort, $stepsNode, $parent);
    }

    public function getLoginBlockNode() : LoginBlockNode
    {
        return $this->loginBlockNode;
    }
}