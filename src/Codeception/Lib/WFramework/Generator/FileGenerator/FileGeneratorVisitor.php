<?php


namespace Codeception\Lib\WFramework\Generator\FileGenerator;


use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\PageObjectNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\StepExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\TestExampleNode;
use Codeception\Lib\WFramework\Generator\ParsingTree\IDescribeClass;

class FileGeneratorVisitor
{
    protected string $projectDir;

    protected string $supportDir;

    protected bool $firstInit;


    public function __construct(string $projectDir, string $supportDir, bool $firstInit = false)
    {
        $this->projectDir = $projectDir;
        $this->supportDir = $supportDir;
        $this->firstInit = $firstInit;
    }

    public function __call(string $name, array $arguments)
    {
        $node = reset($arguments);

        if (!$node instanceof AbstractNode)
        {
            throw new UsageException('Первым аргументов визитора должен быть AbstractNode');
        }

        if (!$node instanceof IDescribeClass || $this->shouldNotGenerate($node))
        {
            return;
        }

        if (empty($node->getSource()) || empty($node->getOutputNamespace()))
        {
            return;
        }

        $subpath = $this->namespaceToPath($node->getOutputNamespace());

        if ($node instanceof TestExampleNode)
        {
            $path = $this->projectDir . $subpath;
        }
        else
        {
            $path = $this->supportDir . $subpath;
        }

        $path = $this->mkDir($path);

        $filename = $path . DIRECTORY_SEPARATOR . $node->getEntityClassShort() . '.php';

        if (file_exists($filename))
        {
            unlink($filename);
        }

        file_put_contents($filename, $node->getSource());
    }

    protected function shouldNotGenerate($node) : bool
    {
        if ($this->firstInit)
        {
            return false;
        }

        $generateOnFirstInitOnly = [PageObjectNode::class, PageObjectExampleNode::class, StepExampleNode::class, TestExampleNode::class];

        foreach ($generateOnFirstInitOnly as $nodeClass)
        {
            if ($node instanceof $nodeClass)
            {
                return true;
            }
        }

        return false;
    }

    protected function namespaceToPath(string $namespace) : string
    {
        $namespace = substr($namespace, strpos($namespace, '\\') + 1);

        return DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    protected function mkDir(string $dir) : string
    {
        if (!is_dir($dir))
        {
            mkdir($dir, 0777, true);
        }

        $result = realpath($dir);

        if (!$result)
        {
            throw new GeneralException('Не получилось создать каталог: ' . $dir);
        }

        return $result;
    }
}
