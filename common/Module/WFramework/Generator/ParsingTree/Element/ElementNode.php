<?php


namespace Common\Module\WFramework\Generator\ParsingTree\Element;


use Common\Module\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;

class ElementNode extends AbstractPageObjectNode
{
    /**
     * @param ElementFacadeNode $facadeNode
     */
    public function addFacade($facadeNode)
    {
        parent::addFacade($facadeNode);
    }

    /**
     * @return ElementFacadeNode
     */
    public function getFacade()
    {
        /** @var ElementFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }
}
