<?php


namespace Common\Module\WFramework\Generator\SourceGenerator;


use Codeception\Util\Template;
use Common\Module\WFramework\Generator\IGenerator;
use Common\Module\WFramework\Helpers\ClassHelper;

class ElementSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Common\Module\WFramework\WebObjects\Base\WElement\WElement;
use {{actor_class_full}};
use {{element_facade_class_full}};

abstract class {{element_class_short}} extends WElement
{
    public function returnCodeceptionActor() : {{actor_class_short}}
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnAdvanced() : {{element_facade_class_short}}
    {
        return $this->advanced ?? $this->advanced = new {{element_facade_class_short}}($this);
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
                        ->place('element_class_short', $this->elementClassShort)
                        ->place('actor_class_full', $this->actorClassFull)
                        ->place('actor_class_short', ClassHelper::getShortName($this->actorClassFull))
                        ->place('element_facade_class_full', $this->elementFacadeClassFull)
                        ->place('element_facade_class_short', ClassHelper::getShortName($this->elementFacadeClassFull))
                        ->produce();
    }
}
