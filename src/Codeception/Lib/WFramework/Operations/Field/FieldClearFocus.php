<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class FieldClearFocus extends AbstractOperation
{
    public function getName() : string
    {
        return "снимаем фокус с поля ввода";
    }

    /**
     * Снимает фокус с элемента
     */
    public function __construct() {}

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        $pageObject->accept(new ExecuteScriptOnThis('arguments[0].blur();'));
    }
}
