<?php


namespace Codeception\Lib\WFramework\Operations\Keyboard;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class KeyboardPressTab extends AbstractOperation
{
    public function getName() : string
    {
        return "посылаем Tab";
    }

    /**
     * Посылает Tab данному элементу
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
            ->should(new Exist())
            ->returnSeleniumElement()
            ->sendKeys(WebDriverKeys::TAB)
            ;
    }
}
