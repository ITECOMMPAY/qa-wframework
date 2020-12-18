<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Operations\Get\GetAttribute;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class CssClass extends AbstractCondition
{
    /**
     * @var string
     */
    protected $className;

    public function getName() : string
    {
        return "CSS класс '$this->className' присутствует?";
    }

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        $classes = $pageObject->accept(new GetAttribute('class')) ?? '';

        $classes = explode(' ', $classes);

        return in_array($this->className, $classes, true);
    }
}
