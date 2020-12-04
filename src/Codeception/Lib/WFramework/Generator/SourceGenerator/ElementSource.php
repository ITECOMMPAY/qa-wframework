<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Util\Template;
use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;

class ElementSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_element_reference}};
use {{actor_class_full}};
use {{element_facade_class_full}};

abstract class {{element_class_short}} extends WElement
{
    public function returnCodeceptionActor() : {{actor_class_short}}
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnOperations() : {{element_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{element_facade_class_short}}($this);
    }
}
EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $elementClassShort;

    /** @var string */
    protected $actorClassFull;

    /** @var string */
    protected $elementFacadeClassFull;

    public function __construct(string $namespace, string $elementClassShort, string $actorClassFull, string $elementFacadeClassFull)
    {
        $this->namespace = $namespace;
        $this->elementClassShort = $elementClassShort;
        $this->actorClassFull = $actorClassFull;
        $this->elementFacadeClassFull = $elementFacadeClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('w_element_reference', WElement::class)
                        ->place('element_class_short', $this->elementClassShort)
                        ->place('actor_class_full', $this->actorClassFull)
                        ->place('actor_class_short', ClassHelper::getShortName($this->actorClassFull))
                        ->place('element_facade_class_full', $this->elementFacadeClassFull)
                        ->place('element_facade_class_short', ClassHelper::getShortName($this->elementFacadeClassFull))
                        ->produce();
    }
}
