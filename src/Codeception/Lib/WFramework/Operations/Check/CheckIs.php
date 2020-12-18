<?php


namespace Codeception\Lib\WFramework\Operations\Check;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

class CheckIs extends AbstractOperation
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    public function getName() : string
    {
        return "проверяем выполнение условия: " . $this->condition;
    }

    /**
     * Проверяет, что заданные условия выполняются для данного элемента.
     *
     * @param AbstractCondition $condition - условие
     */
    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
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
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : bool
    {
        return $pageObject->accept($this->condition);
    }
}
