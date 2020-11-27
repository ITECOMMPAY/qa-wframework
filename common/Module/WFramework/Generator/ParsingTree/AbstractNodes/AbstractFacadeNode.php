<?php


namespace Common\Module\WFramework\Generator\ParsingTree\AbstractNodes;


use Common\Module\WFramework\Exceptions\Common\FrameworkStaledException;
use Common\Module\WFramework\Helpers\ClassHelper;
use Common\Module\WFramework\Helpers\Composite;

abstract class AbstractFacadeNode extends Composite
{
    public $name;

    public $classFull;

    public $outputNamespace;

    public $source = null;

    public function __construct(string $name, string $outputNamespace)
    {
        parent::__construct();

        $this->name = $name;
        $this->classFull = $outputNamespace . '\\' . $name;
        $this->outputNamespace = $outputNamespace;
    }

    /**
     * @param AbstractOperationNode $operationNode
     * @throws FrameworkStaledException
     * @throws \Common\Module\WFramework\Exceptions\Common\UsageException
     */
    public function addOperation($operationNode)
    {
        $groupAndMethod = $this->getGroupAndMethod($operationNode->classFull);

        $groupName = ucfirst($groupAndMethod['group']);
        $methodName = lcfirst($groupAndMethod['method']);

        $operationNode->name = $methodName;

        if (!$this->hasChild($groupName))
        {
            $group = $this->getNewOperationGroup($groupName, $this->outputNamespace);
            $this->addChild($group);
        }

        /** @var AbstractOperationGroupNode $group */
        $group = $this->getChildByName($groupName);
        $group->addOperation($operationNode);
    }

    /**
     * @param string $groupName
     * @param string $outputNamespace
     * @return AbstractOperationGroupNode
     */
    abstract protected function getNewOperationGroup(string $groupName, string $outputNamespace);

    protected function getGroupAndMethod(string $operationClassFull) : array
    {
        $classShort = ClassHelper::getShortName($operationClassFull);

        if (!preg_match('%(?\'group\'[A-Z]+[a-z]+)(?\'method\'.*)%m', $classShort, $matches))
        {
            throw new FrameworkStaledException("Не получилось выделить имя группы  и имя метода из названия класса: $classShort");
        }

        return $matches;
    }

    /**
     * @return AbstractPageObjectNode
     */
    public function getPageObject()
    {
        /** @var AbstractPageObjectNode $parent */
        $parent = $this->getParent();
        return $parent;
    }
}
