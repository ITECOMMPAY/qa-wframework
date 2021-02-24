<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Block;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;

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

    public function getVisitorNames() : array
    {
        return ['accept' . $this->name, 'accept' . ClassHelper::getShortName(WBlock::class)];
    }
}
