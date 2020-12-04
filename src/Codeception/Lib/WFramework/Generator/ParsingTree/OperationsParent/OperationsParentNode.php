<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\OperationsParent;


use Codeception\Lib\WFramework\Helpers\Composite;

class OperationsParentNode extends Composite
{
    public $name;

    public $classFull;

    public $outputNamespace;

    public $source = null;

    public $blockClassFull;

    public $elementClassFull;

    public $collectionClassFull;

    public function __construct(string $name, string $outputNamespace, string $blockClassFull, string $elementClassFull, string $collectionClassFull)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
        $this->blockClassFull = $blockClassFull;
        $this->elementClassFull = $elementClassFull;
        $this->collectionClassFull = $collectionClassFull;
    }
}
