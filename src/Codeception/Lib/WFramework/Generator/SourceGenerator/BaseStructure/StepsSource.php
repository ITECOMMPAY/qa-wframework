<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BaseStructure;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepsNode;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Util\Template;

class StepsSource implements IGenerator
{

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Steps\StepsGroup;
{{step_imports}}

class {{steps_class_short}} extends StepsGroup
{
{{step_fields}}

    public function __construct(
{{step_params}}
    )
    {
{{step_fields_init}}
    }
}
EOF;

    protected const IMPORT_TEMPLATE = <<<'EOF'
use {{step_object_class_full}};
EOF;

    protected const FIELD_TEMPLATE = <<<'EOF'
    /** @var {{step_object_class_short}} */
    public static ${{step_object_variable}};
    
EOF;

    protected const PARAM_TEMPLATE = <<<'EOF'
        {{step_object_class_short}} ${{step_object_variable}}
EOF;

    protected const INIT_TEMPLATE = <<<'EOF'
        static::${{step_object_variable}} = ${{step_object_variable}};
EOF;

    protected StepsNode $node;

    public function __construct(StepsNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $imports = [];
        $fields = [];
        $params = [];
        $inits = [];

        foreach ($this->node->getRootNode()->getStepObjectClassesFull() as $stepObjectClassFull)
        {
            $stepObjectClassShort = ClassHelper::getShortName($stepObjectClassFull);
            $stepObjectVariableName = $this->lowerCamelCase($stepObjectClassShort);

            if ($stepObjectClassShort === $this->node->getEntityClassShort())
            {
                //Зацепили сгенерированный класс
                continue;
            }

            $imports[] = (new Template(static::IMPORT_TEMPLATE))
                                ->place('step_object_class_full', $stepObjectClassFull)
                                ->produce();

            $fields[] =  (new Template(static::FIELD_TEMPLATE))
                                ->place('step_object_class_short', $stepObjectClassShort)
                                ->place('step_object_variable', $stepObjectVariableName)
                                ->produce();

            $params[] =  (new Template(static::PARAM_TEMPLATE))
                                ->place('step_object_class_short', $stepObjectClassShort)
                                ->place('step_object_variable', $stepObjectVariableName)
                                ->produce();

            $inits[] =   (new Template(static::INIT_TEMPLATE))
                                ->place('step_object_variable', $stepObjectVariableName)
                                ->produce();
        }

        $source = (new Template(static::TEMPLATE))
                        ->place('namespace',            $this->node->getOutputNamespace())
                        ->place('steps_class_short',    $this->node->getEntityClassShort())
                        ->place('step_imports',         implode(PHP_EOL, $imports))
                        ->place('step_fields',          implode(PHP_EOL, $fields))
                        ->place('step_params',          implode(',' . PHP_EOL, $params))
                        ->place('step_fields_init',     implode(PHP_EOL, $inits))
                        ->produce();

        $this->node->setSource($source);
    }

    protected function lowerCamelCase(string $input) : string
    {
        if (preg_match('%^(?\'prefix\'[A-Z]+)(?\'word\'[A-Z].*)$%m', $input, $matches) === 1)
        {
            return strtolower($matches['prefix']) . $matches['word'];
        }

        return lcfirst($input);
    }
}