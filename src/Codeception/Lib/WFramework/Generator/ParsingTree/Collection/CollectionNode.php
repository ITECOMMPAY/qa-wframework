<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Collection;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;

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

    public function getVisitorNames() : array
    {
        return ['accept' . $this->name, 'accept' . ClassHelper::getShortName(WCollection::class)];
    }
}
