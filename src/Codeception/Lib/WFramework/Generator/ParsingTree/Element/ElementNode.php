<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Element;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

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

    public function getVisitorNames() : array
    {
        return ['accept' . $this->name, 'accept' . ClassHelper::getShortName(WElement::class)];
    }
}
