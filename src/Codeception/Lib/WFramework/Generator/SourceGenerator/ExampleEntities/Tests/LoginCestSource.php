<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\TestExampleNode;
use Codeception\Util\Template;

class LoginCestSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{steps_class_full}};
use {{actor_class_full}};

/**
 * Class {{class_short}}
 *
 * Локально:           ./vendor/bin/codecept run webui LoginCest -c ./tests/{{project_name}} --env loc-base,loc-bro-chrome,loc-res-1920
 * Через BrowserStack: ./vendor/bin/codecept run webui LoginCest -c ./tests/{{project_name}} --env bstack-base,bstack-bro-chrome,bstack-res-1920,bstack-sys-windows
 * 
 */
class {{class_short}}
{
    public function test01({{actor_class_short}} $I, {{steps_class_short}} $steps)
    {
        $I->wantToTest('Логин в систему');

        $steps::$loginSteps
                        ->openSite()
                        ->login()
                        ;
    }
}
EOF;

    protected TestExampleNode $node;

    public function __construct(TestExampleNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('class_short',          $this->node->getEntityClassShort())
                        ->place('project_name',         strtolower($this->node->getRootNode()->getProjectName()))
                        ->place('actor_class_full',     $this->node->getRootNode()->getActorClassFull())
                        ->place('actor_class_short',    $this->node->getRootNode()->getActorClassShort())
                        ->place('steps_class_full',     $this->node->getStepsNode()->getEntityClassFull())
                        ->place('steps_class_short',    $this->node->getStepsNode()->getEntityClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}