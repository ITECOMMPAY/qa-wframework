<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Steps;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Steps\LoginStepsNode;
use Codeception\Util\Template;

class LoginStepsSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use {{login_block_class_full}};
use {{actor_class_full}};

class {{class_short}} extends StepsGroup
{
    /** @var {{actor_class_short}} */
    protected $I;

    /** @var {{login_block_class_short}} */
    public $loginBlock;

    public function __construct(
        {{actor_class_short}} $I,
        {{login_block_class_short}} $loginBlock
    )
    {
        $this->I = $I;
        $this->loginBlock = $loginBlock;
    }

    public function shouldBeDisplayed() : {{class_short}}
    {
        $this->I->logNotice($this,'Проверяем, что страница логина отобразилась');

        $this
            ->loginBlock
            ->shouldBeDisplayed()
            ;

        return $this;
    }

    public function openSite() : {{class_short}}
    {
        $this->I->resetCookie('PHPSESSID');

        $this
            ->loginBlock
            ->display()
            ;

        return $this;
    }

    public function login() : {{front_page_steps_class_short}}
    {
        $this->I->logNotice($this,'Заходим в систему');

        $this
            ->loginBlock
            ->getEmailField()
            ->set(TestProperties::getValue('email'))
            ;

        $this
            ->loginBlock
            ->getPasswordField()
            ->set(TestProperties::getValue('password'))
            ;

        $this
            ->loginBlock
            ->getLoginButton()
            ->click()
            ;

        return {{steps_class_short}}::$frontPageSteps->shouldBeDisplayed();
    }
}
EOF;

    protected LoginStepsNode $node;

    public function __construct(LoginStepsNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',                    $this->node->getOutputNamespace())
                        ->place('class_short',                  $this->node->getEntityClassShort())
                        ->place('actor_class_full',             $this->node->getStepsNode()->getRootNode()->getActorClassFull())
                        ->place('actor_class_short',            $this->node->getStepsNode()->getRootNode()->getActorClassShort())
                        ->place('login_block_class_full',       $this->node->getLoginBlockNode()->getEntityClassFull())
                        ->place('login_block_class_short',      $this->node->getLoginBlockNode()->getEntityClassShort())
                        ->place('steps_class_short',            $this->node->getStepsNode()->getEntityClassShort())
                        ->place('front_page_steps_class_short', $this->node->getFrontPageStepsNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}