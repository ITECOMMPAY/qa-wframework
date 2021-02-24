<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Collection;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationGroupNode;

class CollectionOperationGroupNode extends AbstractOperationGroupNode
{
    /**
     * @return CollectionFacadeNode
     */
    public function getFacade()
    {
        /** @var CollectionFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }

    /**
     * @param CollectionOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }
}
