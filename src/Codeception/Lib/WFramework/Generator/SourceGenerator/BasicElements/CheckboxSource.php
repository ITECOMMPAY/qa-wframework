<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\BasicElements;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Util\Template;

class CheckboxSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\Conditions\Not_;
use Codeception\Lib\WFramework\Conditions\Selected;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use {{element_class_full}};

class {{checkbox_class_short}} extends {{element_class_short}} implements IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Флаг';
    }

    public function check() : {{checkbox_class_short}}
    {
        WLogger::logAction($this, "ставим");

        if ($this->isUnchecked())
        {
            $this
                ->returnOperations()
                ->mouse()
                ->clickSmart()
                ;
        }

        return $this;
    }

    public function uncheck() : {{checkbox_class_short}}
    {
        WLogger::logAction($this, "снимаем");

        if ($this->isChecked())
        {
            $this
                ->returnOperations()
                ->mouse()
                ->clickSmart()
                ;
        }

        return $this;
    }

    public function isChecked() : bool
    {
        return $this->is(new Selected());
    }

    public function isUnchecked() : bool
    {
        return $this->is(new Not_(new Selected()));
    }

    public function shouldBeChecked() : {{checkbox_class_short}}
    {
        return $this->should(new Selected());
    }

    public function shouldBeUnchecked() : {{checkbox_class_short}}
    {
        return $this->should(new Not_(new Selected()));
    }

    public function finallyChecked() : bool
    {
        return $this->finally_(new Selected());
    }

    public function finallyUnchecked() : bool
    {
        return $this->finally_(new Not_(new Selected()));
    }

    public function getCurrentValueString() : string
    {
        return json_encode($this->isChecked());
    }
}
EOF;

    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $checkboxClassFull;
    /**
     * @var string
     */
    protected $elementClassFull;

    public function __construct(string $namespace, string $checkboxClassFull, string $elementClassFull)
    {
        $this->namespace = $namespace;
        $this->checkboxClassFull = $checkboxClassFull;
        $this->elementClassFull = $elementClassFull;
    }

    public function produce() : string
    {
        return (new Template(static::TEMPLATE))
                        ->place('namespace', $this->namespace)
                        ->place('checkbox_class_short', ClassHelper::getShortName($this->checkboxClassFull))
                        ->place('element_class_full', $this->elementClassFull)
                        ->place('element_class_short', ClassHelper::getShortName($this->elementClassFull))
                        ->produce()
                        ;
    }
}