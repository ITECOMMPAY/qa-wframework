<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Collection;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationNode;

class CollectionOperationNode extends AbstractOperationNode
{
    protected function getVisitorName() : string
    {
        return 'acceptWCollection';
    }
}
