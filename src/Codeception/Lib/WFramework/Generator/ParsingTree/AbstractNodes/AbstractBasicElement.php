<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\ParsingTree\Element\ElementNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\RootNode;
use Codeception\Lib\WFramework\Helpers\Composite;

class AbstractBasicElement extends Composite
{
    public $name;

    public $classFull;

    public $outputNamespace;

    public $source = null;

    public function __construct(string $name, string $outputNamespace)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
    }

    public function getElementNode() : ElementNode
    {
        $rootNode = $this->getFirstParentWithClass(RootNode::class);

        foreach ($rootNode->getChildren() as $name => $child)
        {
            if ($child instanceof ElementNode)
            {
                return $child;
            }
        }

        throw new UsageException($this . ' -> должен быть среди детей RootNode');
    }

}