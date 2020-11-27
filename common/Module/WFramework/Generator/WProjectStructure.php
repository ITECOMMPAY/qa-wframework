<?php


namespace Common\Module\WFramework\Generator;


use Common\Module\WFramework\Generator\FileGenerator\FileGeneratorVisitor;
use Common\Module\WFramework\Generator\ParsingTree\RootNode;
use Common\Module\WFramework\Generator\SourceGenerator\SourceGeneratorVisitor;
use Common\Module\WFramework\Helpers\Composite;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WOperations\AbstractPageObjectVisitor;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;

class WProjectStructure
{
    /** @var string */
    protected $projectName;

    /** @var string */
    protected $outputPath;

    /** @var string */
    protected $outputNamespace;

    /** @var array */
    protected $operationsPath = [];

    /** @var string */
    protected $actorNameShort;

    /** @var string */
    protected $actorNameFull;

    public function __construct(string $actorNameShort, string $actorSuffix, string $supportDir, string $outputNamespace)
    {
        $this->projectName = substr($actorNameShort, 0, strpos($actorNameShort, $actorSuffix));
        $this->outputPath = $supportDir;
        $this->outputNamespace = $outputNamespace;
        $this->actorNameShort = $actorNameShort;
        $this->actorNameFull = $this->getActorNameFull();
        $this->operationsPath = [__DIR__ . '/../WOperations', $supportDir . '/Helper'];
    }

    protected function getActorNameFull() : string
    {
        if (!empty($this->outputNamespace))
        {
            return $this->outputNamespace . '\\' . $this->actorNameShort;
        }

        return $this->actorNameShort;
    }

    public function build()
    {
        $parsingTree = new RootNode($this->projectName, $this->actorNameFull, $this->outputNamespace, $this->getOperationClasses());

        $sourceGenerator = new SourceGeneratorVisitor();
        $parsingTree->callDepthFirst(function (Composite $node) use ($sourceGenerator) {$node->accept($sourceGenerator);});

        $fileGenerator = new FileGeneratorVisitor($this->outputPath);
        $parsingTree->callDepthFirst(function (Composite $node) use ($fileGenerator) {$node->accept($fileGenerator);});
    }

    protected function getOperationClasses() : array
    {
        $result = [];

        foreach ($this->operationsPath as $path)
        {
            if (!is_dir($path))
            {
                WLogger::logDebug("Директория: $path -  не существует");
                continue;
            }

            $result[] = $this->loadSubclassesFromDir($path, AbstractPageObjectVisitor::class);
        }

        return array_merge(...$result);
    }

    protected function loadSubclassesFromDir(string $path, string $classOrInterface) : array
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($phpFiles as $phpFile)
        {
            require_once $phpFile[0];
        }

        $classes = get_declared_classes();

        $result = [];

        foreach ($classes as $class)
        {
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isSubclassOf($classOrInterface))
            {
                $result[$class] = $reflectionClass;
            }
        }

        return $result;
    }
}
