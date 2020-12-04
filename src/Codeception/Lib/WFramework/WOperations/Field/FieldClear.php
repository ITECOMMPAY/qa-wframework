<?php


namespace Codeception\Lib\WFramework\WOperations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldClear extends AbstractOperation
{
    /**
     * Очищает текст элемента (Ctrl+A, Backspace).
     */
    public function __construct() {}

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Очищаем поле');

        $pageObject
            ->getProxyWebElement()
            ->sendKeys([WebDriverKeys::CONTROL, 'a'])
            ->sendKeys(WebDriverKeys::BACKSPACE)
            ->clear()
            ;
    }
}
