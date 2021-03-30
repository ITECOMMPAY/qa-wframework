<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Steps;


use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepsNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block\LoginBlockNode;

class LoginStepsNode extends StepExampleNode
{
    private LoginBlockNode $loginBlockNode;

    private StepExampleNode $frontPageStepsNode;



    public function __construct(
        string $name,
        string $entityClassShort,
        StepsNode $parent,
        LoginBlockNode $loginBlockNode,
        StepExampleNode $frontPageStepsNode
    )
    {
        $this->loginBlockNode = $loginBlockNode;
        $this->frontPageStepsNode = $frontPageStepsNode;

        parent::__construct($name, $entityClassShort, $parent);
    }


    public function getLoginBlockNode() : LoginBlockNode
    {
        return $this->loginBlockNode;
    }

    public function getFrontPageStepsNode() : StepExampleNode
    {
        return $this->frontPageStepsNode;
    }
}