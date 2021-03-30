<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Blocks;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block\LoginBlockNode;
use Codeception\Util\Template;

class LoginBlockSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{block_class_full}};
use {{actor_class_full}};
use {{button_class_full}};
use {{textbox_class_full}};

class LoginBlock extends {{block_class_short}}
{
    protected function initName() : string
    {
        return 'Блок логина';
    }

    protected function openPage()
    {
        $this->returnCodeceptionActor()->amOnPage('');
    }

    public function __construct({{actor_class_short}} $actor)
    {
        $this->emailField =     {{textbox_class_short}}::fromXpath('Email',    ".//input[@name='email']");
        $this->passwordField =  {{textbox_class_short}}::fromXpath('Password', ".//input[starts-with(@name, 'pass')]");

        $this->loginButton =    {{button_class_short}}::fromXpath( 'Login',    ".//button[contains(., 'Login')]");

        parent::__construct($actor);
    }





    public function getEmailField() : {{textbox_class_short}}
    {
        return $this->emailField;
    }

    public function getLoginButton() : {{button_class_short}}
    {
        return $this->loginButton;
    }

    public function getPasswordField() : {{textbox_class_short}}
    {
        return $this->passwordField;
    }
}
EOF;

    protected LoginBlockNode $node;

    public function __construct(LoginBlockNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('block_class_full',     $this->node->getPageObjectNode()->getEntityClassFull())
                        ->place('block_class_short',    $this->node->getPageObjectNode()->getEntityClassShort())
                        ->place('actor_class_full',     $this->node->getPageObjectNode()->getRootNode()->getActorClassFull())
                        ->place('actor_class_short',    $this->node->getPageObjectNode()->getRootNode()->getActorClassShort())
                        ->place('button_class_full',    $this->node->getButton()->getEntityClassFull())
                        ->place('button_class_short',   $this->node->getButton()->getEntityClassShort())
                        ->place('textbox_class_full',   $this->node->getTextBox()->getEntityClassFull())
                        ->place('textbox_class_short',  $this->node->getTextBox()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}