<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes;


use Codeception\Lib\WFramework\Generator\ParsingTree\AbstractNode;
use Codeception\Lib\WFramework\Exceptions\FrameworkStaledException;
use Codeception\Lib\WFramework\Helpers\ClassHelper;

class OperationNode extends AbstractNode
{
    protected string $methodName = '';

    protected string $groupName = '';

    protected string $entityClassFull;

    protected \ReflectionClass $reflectionClass;

    protected ?\ReflectionMethod $reflectionMethod = null;



    protected function __construct(string $operationClassFull, \ReflectionClass $reflectionClass, array $visitorNames)
    {
        $this->setGroupAndMethodFrom($operationClassFull);
        $this->name             = $this->methodName;
        $this->entityClassFull  = $operationClassFull;
        $this->reflectionClass  = $reflectionClass;
        $this->reflectionMethod = $this->newReflectionMethod($visitorNames);

        parent::__construct();
    }

    public static function tryCreateFrom(string $operationClassFull, \ReflectionClass $reflectionClass, array $visitorNames) : ?OperationNode
    {
        $operationNode = new static($operationClassFull, $reflectionClass, $visitorNames);

        if ($operationNode->reflectionMethod === null)
        {
            return null;
        }

        return $operationNode;
    }

    protected function newReflectionMethod(array $visitorNames) : ?\ReflectionMethod
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

    protected function setGroupAndMethodFrom(string $operationClassFull)
    {
        $classShort = ClassHelper::getShortName($operationClassFull);

        if (!preg_match('%(?\'group\'[A-Z]+[a-z]+)(?\'method\'.*)%m', $classShort, $matches))
        {
            throw new FrameworkStaledException("Не получилось выделить имя группы  и имя метода из названия класса: $classShort");
        }

        $this->groupName = ucfirst($matches['group']);
        $this->methodName = lcfirst($matches['method']);

        return $matches;
    }




    public function getOperationGroupNode() : OperationGroupNode
    {
        /** @var OperationGroupNode $operationGroup */
        $operationGroup = $this->getParent();
        return $operationGroup;
    }

    public function getMethodName() : string
    {
        return $this->methodName;
    }

    public function getGroupName() : string
    {
        return $this->groupName;
    }

    public function getEntityClassFull() : string
    {
        return $this->entityClassFull;
    }

    public function getReflectionClass() : \ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function getReflectionMethod() : \ReflectionMethod
    {
        return $this->reflectionMethod;
    }
}
