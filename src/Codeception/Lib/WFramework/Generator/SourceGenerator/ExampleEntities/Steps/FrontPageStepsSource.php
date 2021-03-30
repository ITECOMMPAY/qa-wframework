<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Steps;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepExampleNode;
use Codeception\Util\Template;

class FrontPageStepsSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Steps\StepsGroup;
use {{actor_class_full}};

class {{class_short}} extends StepsGroup
{
    /** @var {{actor_class_short}} */
    protected $I;

    public function __construct(
        {{actor_class_short}} $I
    )
    {
        $this->I = $I;
    }

    public function shouldBeDisplayed() : {{class_short}}
    {
        $this->I->logNotice($this,'Проверяем, что главная страница отобразилась');

        //...

        return $this;
    }
}
EOF;

    protected StepExampleNode $node;

    public function __construct(StepExampleNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('class_short',          $this->node->getEntityClassShort())
                        ->place('actor_class_full',     $this->node->getStepsNode()->getRootNode()->getActorClassFull())
                        ->place('actor_class_short',    $this->node->getStepsNode()->getRootNode()->getActorClassShort())
                        ->produce();

        $this->node->setSource($source);
    }
}