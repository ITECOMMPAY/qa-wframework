<?php


namespace Common\Module\WFramework\Generator\SourceGenerator;


use Codeception\Util\Template;
use Common\Module\WFramework\Generator\IGenerator;
use Common\Module\WFramework\Helpers\ClassHelper;

class BlockSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Common\Module\WFramework\WebObjects\Base\WBlock\WBlock;
use {{actor_class_full}};
use {{block_facade_class_full}};

abstract class {{block_class_short}} extends WBlock
{
    public function __construct({{actor_class_short}} $actor)
    {
        parent::__construct($actor);
    }
    
    public function returnCodeceptionActor() : {{actor_class_short}}
    {
        return parent::returnCodeceptionActor();
    }
   
    public function returnAdvanced() : {{block_facade_class_short}}
    {
        return $this->advanced ?? $this->advanced = new {{block_facade_class_short}}($this);
    }
}
EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $blockClassShort;

    /** @var string */
    protected $actorClassFull;

    /** @var string */
    protected $blockFacadeClassFull;

    public function __construct(string $namespace, string $blockClassShort, string $actorClassFull, string $blockFacadeClassFull)
    {
        $this->namespace = $namespace;
        $this->blockClassShort = $blockClassShort;
        $this->actorClassFull = $actorClassFull;
        $this->blockFacadeClassFull = $blockFacadeClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('block_class_short', $this->blockClassShort)
                        ->place('actor_class_full', $this->actorClassFull)
                        ->place('actor_class_short', ClassHelper::getShortName($this->actorClassFull))
                        ->place('block_facade_class_full', $this->blockFacadeClassFull)
                        ->place('block_facade_class_short', ClassHelper::getShortName($this->blockFacadeClassFull))
                        ->produce();
    }
}
