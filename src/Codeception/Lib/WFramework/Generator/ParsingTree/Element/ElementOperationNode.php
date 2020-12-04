<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Element;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;

class ElementOperationNode extends AbstractOperationNode
{
    protected function getVisitorName() : string
    {
        return 'acceptWCollection';
    }
}
