<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;
use Facebook\WebDriver\WebDriverDimension;

class GetScrollSize extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем реальный размер (scrollWidth x scrollHeight)";
    }

    /**
     * Возвращает реальный размер элемента (scrollWidth x scrollHeight)
     */
    public function __construct() {}

    public function acceptWBlock($block) : WebDriverDimension
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : WebDriverDimension
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : WebDriverDimension
    {
        $size = $pageObject->returnSeleniumElement()->executeScriptOnThis('return {"width": arguments[0].scrollWidth, "height": arguments[0].scrollHeight};');

        WLogger::logDebug('Реальный размер элемента: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
