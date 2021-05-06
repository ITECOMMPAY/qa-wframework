<?php


namespace Codeception\Lib\WFramework\Operations\Find;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class FindElement extends AbstractOperation
{
    public function getName() : string
    {
        return "ищем новый элемент относительно текущего по локатору: " . $this->by;
    }

    /**
     * @var WLocator
     */
    protected $by;

    /**
     * Ищет новый элемент относительно текущего.
     *
     * @param \Codeception\Lib\WFramework\WLocator\WLocator $by - локатор относительного элемента. Не забывайте, что в случае XPath необходимо чтобы локатор
     *                       относительного элемента начинался с точки.
     */
    public function __construct(WLocator $by)
    {
        $this->by = $by;
    }

    public function acceptWBlock($block) : ProxyWebElement
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : ProxyWebElement
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : ProxyWebElement
    {
        return $pageObject
                    ->shouldExist()
                    ->returnSeleniumElement()
                    ->findElement($this->by)
                    ;
    }
}
