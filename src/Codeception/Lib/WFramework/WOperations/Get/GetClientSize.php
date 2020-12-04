<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverDimension;

class GetClientSize extends AbstractOperation
{
    /**
     * Возвращает внутренний размер элемента без границ и полос прокруток (clientWidth x clientHeight)
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : WebDriverDimension
    {
        WLogger::logDebug('Получаем внутренний размер элемента без границ и полос прокруток (clientWidth x clientHeight)');

        $size = $pageObject->getProxyWebElement()->executeScriptOnThis('return {"width": arguments[0].clientWidth, "height": arguments[0].clientHeight};');

        WLogger::logDebug('Внутренний размер элемента: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
