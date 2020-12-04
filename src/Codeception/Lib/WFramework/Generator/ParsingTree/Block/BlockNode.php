<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Block;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;

class BlockNode extends AbstractPageObjectNode
{
    /**
     * @param BlockFacadeNode $facadeNode
     */
    public function addFacade($facadeNode)
    {
        parent::addFacade($facadeNode);
    }

    /**
     * @return BlockFacadeNode
     */
    public function getFacade()
    {
        /** @var BlockFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }
}
