<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Collection;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;

class CollectionNode extends AbstractPageObjectNode
{
    /**
     * @param CollectionFacadeNode $facadeNode
     */
    public function addFacade($facadeNode)
    {
        parent::addFacade($facadeNode);
    }

    /**
     * @return CollectionFacadeNode
     */
    public function getFacade()
    {
        /** @var CollectionFacadeNode $facade */
        $facade = parent::getFacade();
        return $facade;
    }
}
