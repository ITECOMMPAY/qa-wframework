<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Collection;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractFacadeNode;

class CollectionFacadeNode extends AbstractFacadeNode
{
    protected function getNewOperationGroup(string $groupName, string $outputNamespace)
    {
        return new CollectionOperationGroupNode($groupName, $outputNamespace);
    }

    /**
     * @param CollectionOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }

    /**
     * @return CollectionNode
     */
    public function getPageObject()
    {
        /** @var CollectionNode $block */
        $block = parent::getPageObject();
        return $block;
    }
}
