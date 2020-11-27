<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Element;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;

class ElementOperationNode extends AbstractOperationNode
{
    protected function getVisitorName() : string
    {
        return 'acceptWElement';
    }
}
