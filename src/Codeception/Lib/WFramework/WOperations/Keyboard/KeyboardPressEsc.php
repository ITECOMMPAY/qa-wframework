<?php


namespace Codeception\Lib\WFramework\WOperations\Keyboard;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class KeyboardPressEsc extends AbstractOperation
{
    /**
     * Посылает Esc данному элементу.
     */
    public function __construct() {}

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function acceptWCollection($collection)
    {
        $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Посылаем элементу Esc');

        $pageObject
            ->getProxyWebElement()
            ->sendKeys(WebDriverKeys::ESCAPE)
            ;
    }
}
