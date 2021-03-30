<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block;


use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectNode;

class LoginBlockNode extends PageObjectExampleNode
{
    private PageObjectExampleNode $button;

    private PageObjectExampleNode $textBox;



    public function __construct(
        string $name,
        string $entityClassShort,
        PageObjectNode $parent,
        PageObjectExampleNode $button,
        PageObjectExampleNode $textBox
    )
    {
        $this->button = $button;
        $this->textBox = $textBox;

        parent::__construct($name, $entityClassShort, $parent);
    }


    public function getButton() : PageObjectExampleNode
    {
        return $this->button;
    }

    public function getTextBox() : PageObjectExampleNode
    {
        return $this->textBox;
    }
}