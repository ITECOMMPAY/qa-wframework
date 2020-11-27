<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Element;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractFacadeNode;

class ElementFacadeNode extends AbstractFacadeNode
{
    protected function getNewOperationGroup(string $groupName, string $outputNamespace)
    {
        return new ElementOperationGroupNode($groupName, $outputNamespace);
    }

    /**
     * @param ElementOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }

    /**
     * @return ElementNode
     */
    public function getPageObject()
    {
        /** @var ElementNode $block */
        $block = parent::getPageObject();
        return $block;
    }
}
