<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;
use Codeception\Lib\WFramework\Helpers\ClassHelper;

class PageObjectNode extends AbstractNode implements IDescribeClass
{
    protected string $entityClassShort;

    protected string $entityClassFull;

    protected string $outputNamespace;

    protected string $basePageObjectClassShort;

    protected string $basePageObjectClassFull;



    public function __construct(string $name, string $basePageObjectClassFull, RootNode $parent)
    {
        $this->setParent($parent);

        $this->name                     = $name;
        $this->outputNamespace          = $this->getRootNode()->getHelperNamespace() . '\\' . $name . 's';
        $this->basePageObjectClassShort = ClassHelper::getShortName($basePageObjectClassFull);
        $this->basePageObjectClassFull  = $basePageObjectClassFull;
        $this->entityClassShort         = $this->getRootNode()->getProjectName() . $name;
        $this->entityClassFull          = $this->outputNamespace . '\\' . $this->entityClassShort;

        parent::__construct();

    }

    public function addFacadeNode(string $classShort) : FacadeNode
    {
        $facadeNode = new FacadeNode($classShort, $this);
        $this->addChild($facadeNode);
        return $facadeNode;
    }

    public function addExampleNode(string $name, string $classShort) : PageObjectExampleNode
    {
        $exampleNode = new PageObjectExampleNode($name, $classShort, $this);
        $this->addChild($exampleNode);
        return $exampleNode;
    }

    public function addExampleNodeExisting(PageObjectExampleNode $node) : PageObjectExampleNode
    {
        $this->addChild($node);
        return $node;
    }




    public function getRootNode() : RootNode
    {
        /** @var RootNode $rootNode */
        $rootNode = $this->getParent();

        return $rootNode;
    }

    public function getFacadeNode() : FacadeNode
    {
        if ($this->getChildren()->isEmpty())
        {
            throw new UsageException($this . ' -> не задан FacadeNode');
        }

        return $this->getChildren()->first()->value;
    }

    public function getExampleNode(string $name) : PageObjectExampleNode
    {
        /** @var PageObjectExampleNode $node */
        $node =  $this->getChildByName($name);
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

    public function getBasePageObjectClassShort() : string
    {
        return $this->basePageObjectClassShort;
    }

    public function getBasePageObjectClassFull() : string
    {
        return $this->basePageObjectClassFull;
    }
}
