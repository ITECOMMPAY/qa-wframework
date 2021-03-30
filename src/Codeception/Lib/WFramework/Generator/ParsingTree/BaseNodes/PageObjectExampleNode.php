<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;

class PageObjectExampleNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;



    public function __construct(string $name, string $entityClassShort, PageObjectNode $parent)
    {
        $this->setParent($parent);

        $this->name             = $name;
        $this->outputNamespace  = $this->getPageObjectNode()->getOutputNamespace() . '\\' . 'Example';
        $this->entityClassShort = $entityClassShort;
        $this->entityClassFull  = $this->outputNamespace . '\\' . $entityClassShort;

        parent::__construct();
    }



    public function getPageObjectNode() : PageObjectNode
    {
        /** @var PageObjectNode $node */
        $node = $this->getParent();
        return $node;
    }

    public function getEntityClassShort() : string
    {
        return $this->entityClassShort;
    }

    public function getEntityClassFull() : string
    {
        return $this->entityClassFull;
    }

    public function getOutputNamespace() : string
    {
        return $this->outputNamespace;
    }
}