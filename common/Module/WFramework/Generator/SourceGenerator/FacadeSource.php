<?php


namespace Common\Module\WFramework\Generator\SourceGenerator;


use Codeception\Lib\Generator\Shared\Classname;
use Codeception\Util\Shared\Namespaces;
use Codeception\Util\Template;
use Common\Module\WFramework\Generator\IGenerator;
use Common\Module\WFramework\Helpers\ClassHelper;

class FacadeSource implements IGenerator
{
    use Classname;
    use Namespaces;

    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{page_object_class_full}};

class {{facade_class_short}}
{
    /** @var {{page_object_class_short}} */
    protected $pageObject;

    public function __construct({{page_object_class_short}} $pageObject)
    {
        $this->pageObject = $pageObject;
    }
    
    public function getPageObject() : {{page_object_class_short}}
    {
        return $this->pageObject;
    }

    {{groups}}
}
EOF;

    protected const GROUP_GETTER_TEMPLATE = <<<'EOF'

    public function {{group_name}}() : \{{group_class_full}}
    {
        return $this->{{group_name}} ?? $this->{{group_name}} = new \{{group_class_full}}($this);
    }
EOF;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $facadeClassShort;

    /** @var string */
    protected $pageObjectClassFull;

    /** @var string[] */
    protected $groupNameToGroupClassFull;


    public function __construct(string $namespace, string $facadeClassShort, string $pageObjectClassFull, array $groupNameToGroupClassFull)
    {
        $this->namespace = $namespace;
        $this->facadeClassShort = $facadeClassShort;
        $this->pageObjectClassFull = $pageObjectClassFull;
        $this->groupNameToGroupClassFull = $groupNameToGroupClassFull;
    }

    public function produce() : string
    {
        $groups = [];

        foreach ($this->groupNameToGroupClassFull as $groupName => $groupClassFull)
        {
            $groups[] = (new Template(static::GROUP_GETTER_TEMPLATE))
                                ->place('group_name', lcfirst($groupName))
                                ->place('group_class_full', $groupClassFull)
                                ->produce();
        }

        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('facade_class_short', $this->facadeClassShort)
                        ->place('page_object_class_full', $this->pageObjectClassFull)
                        ->place('page_object_class_short', ClassHelper::getShortName($this->pageObjectClassFull))
                        ->place('groups', implode(PHP_EOL, $groups))
                        ->produce();
    }
}
