<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Element;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractOperationGroupNode;

class ElementOperationGroupNode extends AbstractOperationGroupNode
{
    /**
     * @return ElementFacadeNode
     */
    public function getFacade()
    {
        /** @var ElementFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }

    /**
     * @param ElementOperationNode $operationNode
     */
    public function addOperation($operationNode)
    {
        parent::addOperation($operationNode);
    }
}
