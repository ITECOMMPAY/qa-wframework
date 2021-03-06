<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Util\Template;

class BlockSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_block_reference}};
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
   
    public function returnOperations() : {{block_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{block_facade_class_short}}($this);
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
                        ->place('w_block_reference', WBlock::class)
                        ->place('block_class_short', $this->blockClassShort)
                        ->place('actor_class_full', $this->actorClassFull)
                        ->place('actor_class_short', ClassHelper::getShortName($this->actorClassFull))
                        ->place('block_facade_class_full', $this->blockFacadeClassFull)
                        ->place('block_facade_class_short', ClassHelper::getShortName($this->blockFacadeClassFull))
                        ->produce();
    }
}
