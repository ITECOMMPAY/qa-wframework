<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNodes;


use Codeception\Lib\WFramework\Helpers\Composite;

abstract class AbstractOperationNode extends Composite
{
    public $name;

    public $classFull;

    public $reflectionClass;

    public $reflectionMethod;

    public $source = null;

    protected function __construct(string $operationClassFull, \ReflectionClass $reflectionClass)
    {
        parent::__construct();

        $this->classFull = $operationClassFull;
        $this->reflectionClass = $reflectionClass;
        $this->reflectionMethod = $this->getReflectionMethod();
    }

    /**
     * @param string $operationClassFull
     * @return static|null
     */
    public static function tryFrom(string $operationClassFull, \ReflectionClass $reflectionClass)
    {
        $operationNode = new static($operationClassFull, $reflectionClass);

        if ($operationNode->reflectionMethod === null)
        {
            return null;
        }

        return $operationNode;
    }

    abstract protected function getVisitorName() : string;

    protected function getReflectionMethod() : ?\ReflectionMethod
    {
        $inherited = function ($reflectionMethod, $reflectionClass) {
            return $reflectionMethod->getDeclaringClass()->getName() !== $reflectionClass->getName();
        };

        $methodName = $this->getVisitorName();

        if (!$this->reflectionClass->hasMethod($methodName))
        {
            return null;
        }

        $reflectionMethod = $this->reflectionClass->getMethod($methodName);

        if ($inherited($reflectionMethod, $this->reflectionClass))
        {
            return null;
        }

        return $reflectionMethod;
    }

    /**
     * @return AbstractOperationGroupNode
     */
    public function getOperationGroup()
    {
        /** @var AbstractOperationGroupNode $group */
        $group = $this->getParent();
        return $group;
    }
}
