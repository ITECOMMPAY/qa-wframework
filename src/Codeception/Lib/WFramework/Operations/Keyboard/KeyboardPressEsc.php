<?php


namespace Codeception\Lib\WFramework\Operations\Keyboard;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class KeyboardPressEsc extends AbstractOperation
{
    public function getName() : string
    {
        return "посылаем Esc";
    }

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
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        $pageObject
            ->returnSeleniumElement()
            ->sendKeys(WebDriverKeys::ESCAPE)
            ;
    }
}
