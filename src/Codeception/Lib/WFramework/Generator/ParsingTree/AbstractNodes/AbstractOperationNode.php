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

    protected function __construct(string $operationClassFull, \ReflectionClass $reflectionClass, array $visitorNames)
    {
        parent::__construct();

        $this->classFull = $operationClassFull;
        $this->reflectionClass = $reflectionClass;
        $this->reflectionMethod = $this->getReflectionMethod($visitorNames);
    }

    public static function tryCreateFrom(string $operationClassFull, \ReflectionClass $reflectionClass, array $visitorNames)
    {
        $operationNode = new static($operationClassFull, $reflectionClass, $visitorNames);

        if ($operationNode->reflectionMethod === null)
        {
            return null;
        }

        return $operationNode;
    }

    protected function getReflectionMethod(array $visitorNames) : ?\ReflectionMethod
    {
        foreach ($visitorNames as $methodName)
        {
            if (!$this->reflectionClass->hasMethod($methodName))
            {
                continue;
            }

            return $this->reflectionClass->getMethod($methodName);
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
