<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Block;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationGroupNode;

class BlockOperationGroupNode extends AbstractOperationGroupNode
{
    /**
     * @return BlockFacadeNode
     */
    public function getFacade()
    {
        /** @var BlockFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }

    /**
     * @param BlockOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }

}
