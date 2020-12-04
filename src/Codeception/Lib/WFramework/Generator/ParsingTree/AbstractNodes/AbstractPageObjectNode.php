<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes;


use Codeception\Lib\WFramework\Helpers\Composite;

abstract class AbstractPageObjectNode extends Composite
{
    public $name;

    public $classFull;

    public $actorClassFull;

    public $outputNamespace;

    public $source = null;

    public function __construct(string $name, string $outputNamespace, string $actorClassFull)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
        $this->actorClassFull = $actorClassFull;
    }

    /**
     * @param AbstractFacadeNode $facadeNode
     */
    public function addFacade($facadeNode)
    {
        $this->addChild($facadeNode);
    }

    /**
     * @return AbstractFacadeNode
     */
    public function getFacade()
    {
        /** @var AbstractFacadeNode[] $children */
        $children = $this->getChildren();
        return reset($children);
    }
}
