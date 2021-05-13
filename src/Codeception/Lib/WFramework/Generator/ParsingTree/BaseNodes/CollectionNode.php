<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


class CollectionNode extends PageObjectNode
{
    private PageObjectNode $projectElement;

    public function __construct(
        string $name,
        string $basePageObjectClassFull,
        RootNode $parent,
        PageObjectNode $projectElement
    )
    {
        $this->projectElement = $projectElement;

        parent::__construct($name, $basePageObjectClassFull, $parent);
    }

    public function getProjectElement() : PageObjectNode
    {
        return $this->projectElement;
    }
}