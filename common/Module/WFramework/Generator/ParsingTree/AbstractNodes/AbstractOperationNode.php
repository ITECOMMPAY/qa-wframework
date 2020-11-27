<?php


namespace Common\Module\WFramework\Generator\ParsingTree\AbstractNodes;


use Common\Module\WFramework\Helpers\Composite;

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
            return $reflectionMethod->getDeclaringClass()->name !== $reflectionClass->name;
        };

        $methodNames = [$this->getVisitorName(), 'acceptWPageObject'];

        foreach ($methodNames as $methodName)
        {
            if (!$this->reflectionClass->hasMethod($methodName))
            {
                continue;
            }

            $reflectionMethod = $this->reflectionClass->getMethod($methodName);

            if ($inherited($reflectionMethod, $this->reflectionClass))
            {
                continue;
            }

            return $reflectionMethod;
        }

        return null;
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
