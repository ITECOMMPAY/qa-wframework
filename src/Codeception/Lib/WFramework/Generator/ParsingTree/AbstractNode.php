<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree;


use Codeception\Lib\WFramework\Helpers\Composite;

abstract class AbstractNode extends Composite
{
    protected ?string $source = null;

    public function getSource() : ?string
    {
        return $this->source;
    }

    public function setSource(string $source) : void
    {
        $this->source = $source;
    }
}