<?php


namespace Codeception\Lib\WFramework\Generator;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\FileGenerator\FileGeneratorVisitor;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\RootNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Block\LoginBlockNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Steps\LoginStepsNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Tests\SelfCheckCestNode;
use Codeception\Lib\WFramework\Generator\SourceGenerator\SourceGeneratorVisitor;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Ds\Map;
use Ds\Set;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;

class WProjectStructure
{
    protected string $projectName;

    protected string $actorClassShort;

    protected string $actorClassFull;

    protected string $supportNamespace;

    protected string $testsNamespace;

    protected string $projectDir;

    protected string $supportDir;

    protected string $testsDir;

    protected array $operationsPath = [];

    protected string $stepObjectsPath;

    protected bool $firstInit;


    public function __construct(string $projectName, string $actorClassShort, string $outputNamespace, string $projectDirFull, string $supportDirFull, string $testsDirFull, array $commonDirs = [], bool $firstInit = false)
    {
        $this->projectName             = $projectName;
        $this->projectDir              = realpath($projectDirFull);
        $this->supportDir              = realpath($supportDirFull);
        $this->testsDir                = realpath($testsDirFull);
        $this->codeceptionConfigSubdir = $this->getCodeceptionConfigSubdir($projectDirFull);
        $this->supportNamespace        = $outputNamespace;
        $this->testsNamespace          = $this->getTestsNamespace($outputNamespace, $projectDirFull, $testsDirFull);
        $this->actorClassShort         = $actorClassShort;
        $this->actorClassFull          = empty($outputNamespace) ? $actorClassShort : $outputNamespace . '\\' . $actorClassShort;
        $this->operationsPath          = array_merge([__DIR__ . '/../Operations'], $this->getCommonDirsFull($commonDirs), [$supportDirFull . '/Helper/Operations']);
        $this->stepObjectsPath         = $supportDirFull . '/Helper/Steps';
        $this->firstInit               = $firstInit;
    }

    protected function getTestsNamespace(string $outputNamespace, string $projectDirFull, string $testsDirFull) : string
    {
        $testsSubDir = mb_substr($testsDirFull, mb_strpos($testsDirFull, $projectDirFull) + mb_strlen($projectDirFull));

        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $testsSubDir));

        if (!empty($outputNamespace))
        {
            array_unshift($parts, $outputNamespace);
        }

        return implode('\\', $parts);
    }

    protected function findComposerRootDir(string $dir) : string
    {
        if ($dir === '/' || !is_dir($dir))
        {
            throw new UsageException('Не найден composer.json');
        }

        $dir = dirname($dir);

        if (file_exists("$dir/composer.json"))
        {
            return $dir;
        }

        return $this->findComposerRootDir($dir);
    }

    protected function getCodeceptionConfigSubdir(string $projectDirFull) : string
    {
        $composerRoot = $this->findComposerRootDir($projectDirFull);

        return '.' . mb_substr($projectDirFull, mb_strpos($projectDirFull, $composerRoot) + mb_strlen($composerRoot));
    }

    protected function getCommonDirsFull(array $commonDirs) : array
    {
        $rootDir = $this->findComposerRootDir(codecept_root_dir());

        $result = [];

        foreach ($commonDirs as $commonDir)
        {
            $fullCommonDir = "$rootDir/$commonDir";

            if (!is_dir($fullCommonDir))
            {
                continue;
            }

            $result[] = $fullCommonDir;
        }

        return $result;
    }

    public function build()
    {
        $parsingTree = $this->makeTree();

        $sourceGenerator = new SourceGeneratorVisitor();

        foreach ($parsingTree->traverseDepthFirst() as $node)
        {
            $node->accept($sourceGenerator);
        }

        $fileGenerator = new FileGeneratorVisitor($this->projectDir, $this->supportDir, $this->firstInit);

        foreach ($parsingTree->traverseDepthFirst() as $node)
        {
            $node->accept($fileGenerator);
        }
    }

    protected function makeTree() : RootNode
    {
        $parsingTree = new RootNode($this->projectName, $this->actorClassFull, $this->supportNamespace, $this->testsNamespace, $this->codeceptionConfigSubdir, $this->getOperationClasses(), $this->getStepObjectsClasses());

        $parsingTree
            ->addPageObjectNode('Block', WBlock::class)
            ->addFacadeNode('Operations')
            ->addOperations()
        ;

        $parsingTree
            ->addPageObjectNode('Element', WElement::class)
            ->addFacadeNode('Operations')
            ->addOperations()
        ;

        $parsingTree
            ->addPageObjectNode('Collection', WCollection::class)
            ->addFacadeNode('Operations')
            ->addOperations()
        ;

        $parsingTree->addStepsNode('Steps');

        if (!$this->firstInit)
        {
            return $parsingTree;
        }

        $elementNode = $parsingTree->getPageObjectNode('Element');
        $button     = $elementNode->addExampleNode('Button', $this->projectName . 'Button');
        $checkbox   = $elementNode->addExampleNode('Checkbox', $this->projectName . 'Checkbox');
        $link       = $elementNode->addExampleNode('Link', $this->projectName . 'Link');
        $image      = $elementNode->addExampleNode('Image', $this->projectName . 'Image');
        $label      = $elementNode->addExampleNode('Label', $this->projectName . 'Label');
        $textBox    = $elementNode->addExampleNode('TextBox', $this->projectName . 'TextBox');

        $blockNode = $parsingTree->getPageObjectNode('Block');
        /** @var LoginBlockNode $loginBlock */
        $loginBlock = $blockNode->addExampleNodeExisting(new LoginBlockNode('LoginBlock', 'LoginBlock', $blockNode, $button, $textBox));

        $stepsNode = $parsingTree->getStepsNode('Steps');
        $frontPageSteps = $stepsNode->addExampleNode('FrontPageSteps', 'FrontPageSteps');
        $loginSteps = $stepsNode->addExampleNodeExisting(new LoginStepsNode('LoginSteps', 'LoginSteps', $stepsNode, $loginBlock, $frontPageSteps));

        $parsingTree->addTestExampleNode('LoginCest', $stepsNode);
        $parsingTree->addTestExampleNode('storeShotsCest', $stepsNode);
        $parsingTree->addTestExampleNodeExisting(new SelfCheckCestNode('selfCheckCest', $stepsNode, $parsingTree, $loginBlock));

        return $parsingTree;
    }

    protected function getOperationClasses() : Map
    {
        $result = new Map();

        foreach ($this->operationsPath as $path)
        {
            if (!is_dir($path))
            {
                WLogger::logDebug($this, "Директория: $path -  не существует");
                continue;
            }

            $result->putAll($this->loadSubclassesFromDir($path, AbstractOperation::class));
        }

        $result->sort();

        return $result;
    }

    protected function getStepObjectsClasses() : Set
    {
        $result = new Set();

        if (!is_dir($this->stepObjectsPath))
        {
            return $result;
        }

        $result = $this
                    ->loadSubclassesFromDir($this->stepObjectsPath, StepsGroup::class)
                    ->keys()
                    ;

        $result->sort();

        return $result;
    }

    /**
     * @param string $path
     * @param string $classOrInterface
     * @return array - [class full name => reflection object]
     * @throws \ReflectionException
     */
    protected function loadSubclassesFromDir(string $path, string $classOrInterface) : Map
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($phpFiles as $phpFile)
        {
            require_once $phpFile[0];
        }

        $classes = get_declared_classes();

        $result = new Map();

        foreach ($classes as $class)
        {
            $reflectionClass = new ReflectionClass($class);

            if (!$reflectionClass->isInstantiable())
            {
                continue;
            }

            if (!$reflectionClass->isSubclassOf($classOrInterface))
            {
                continue;
            }

            $result->put($class, $reflectionClass);
        }

        return $result;
    }
}
