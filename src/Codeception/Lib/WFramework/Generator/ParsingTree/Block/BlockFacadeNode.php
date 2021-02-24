<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Block;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractFacadeNode;

class BlockFacadeNode extends AbstractFacadeNode
{
    protected function getNewOperationGroup(string $groupName, string $outputNamespace)
    {
        return new BlockOperationGroupNode($groupName, $outputNamespace);
    }

    /**
     * @param BlockOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }

    /**
     * @return BlockNode
     */
    public function getPageObject()
    {
        /** @var BlockNode $block */
        $block = parent::getPageObject();
        return $block;
    }
}
