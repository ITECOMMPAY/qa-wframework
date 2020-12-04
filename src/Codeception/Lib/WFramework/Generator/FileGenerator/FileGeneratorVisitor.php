<?php


namespace Codeception\Lib\WFramework\Generator\FileGenerator;


use Codeception\Lib\WFramework\Exceptions\Common\GeneralException;
use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes\AbstractPageObjectNode;
use Codeception\Lib\WFramework\Helpers\Composite;

class FileGeneratorVisitor
{
    /**
     * @var string
     */
    protected $outputPath;

    public function __construct(string $outputPath)
    {
        $this->outputPath = $this->mkDir($outputPath);
    }

    public function __call(string $name, array $arguments)
    {
        $node = reset($arguments);

        if (!$node instanceof Composite)
        {
            throw new UsageException('Первым аргументов визитора должен быть Composite');
        }

        if (empty($node->outputNamespace) || empty($node->source))
        {
            return;
        }

        $folder = $this->namespaceToPath($node->outputNamespace);

        $folder = $this->mkDir($folder);

        $filename = $folder . '/' . $node->getName() . '.php';

        if ($node instanceof AbstractPageObjectNode && file_exists($filename))
        {
            return;
        }

        file_put_contents($filename, $node->source);
    }

    protected function namespaceToPath(string $namespace) : string
    {
        $namespace = substr($namespace, strpos($namespace, '\\') + 1);

        return $this->outputPath . '/' . str_replace('\\', '/', $namespace);
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
