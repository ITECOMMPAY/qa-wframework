<?php


namespace Codeception\Lib\WFramework\WOperations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class FieldClearFocus extends AbstractOperation
{
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
        WLogger::logDebug('Снимаем фокус с элемента: ');

        $pageObject->getProxyWebElement()->executeScriptOnThis('arguments[0].blur();');
    }
}
