<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Util\Template;

class CollectionSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{w_collection_reference}};
use {{actor_class_full}};
use {{collection_facade_class_full}};

abstract class {{collection_class_short}} extends WCollection
{
    public function returnOperations() : {{collection_facade_class_short}}
    {
        return $this->operations ?? $this->operations = new {{collection_facade_class_short}}($this);
    }
}
EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $collectionClassShort;

    /** @var string */
    protected $actorClassFull;

    /** @var string */
    protected $collectionFacadeClassFull;

    public function __construct(string $namespace, string $collectionClassShort, string $actorClassFull, string $collectionFacadeClassFull)
    {
        $this->namespace = $namespace;
        $this->collectionClassShort = $collectionClassShort;
        $this->actorClassFull = $actorClassFull;
        $this->collectionFacadeClassFull = $collectionFacadeClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
            ->place('namespace', $this->namespace)
            ->place('w_collection_reference', WCollection::class)
            ->place('collection_class_short', $this->collectionClassShort)
            ->place('actor_class_full', $this->actorClassFull)
            ->place('actor_class_short', ClassHelper::getShortName($this->actorClassFull))
            ->place('collection_facade_class_full', $this->collectionFacadeClassFull)
            ->place('collection_facade_class_short', ClassHelper::getShortName($this->collectionFacadeClassFull))
            ->produce();
    }
}
