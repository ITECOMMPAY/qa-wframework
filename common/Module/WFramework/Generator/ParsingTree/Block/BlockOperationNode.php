<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Block;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;

class BlockOperationNode extends AbstractOperationNode
{
    protected function getVisitorName() : string
    {
        return 'acceptWBlock';
    }
}
