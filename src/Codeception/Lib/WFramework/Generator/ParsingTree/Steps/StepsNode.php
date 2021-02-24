<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\Steps;


use Codeception\Lib\WFramework\Helpers\Composite;

class StepsNode extends Composite
{
    public $name;

    public $classFull;

    public $outputNamespace;

    public $source = null;

    /** @var string[] */
    public $stepObjectClassesFull;

    public function __construct(string $name, string $outputNamespace, array $stepObjectClassesFull)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
        $this->stepObjectClassesFull = $stepObjectClassesFull;
    }
}