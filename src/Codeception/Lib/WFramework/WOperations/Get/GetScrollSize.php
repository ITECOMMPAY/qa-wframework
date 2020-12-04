<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverDimension;

class GetScrollSize extends AbstractOperation
{
    /**
     * Возвращает вреальный размер элемента (scrollWidth x scrollHeight)
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
        WLogger::logDebug('Получаем реальный размер элемента (scrollWidth x scrollHeight)');

        $size = $pageObject->getProxyWebElement()->executeScriptOnThis('return {"width": arguments[0].scrollWidth, "height": arguments[0].scrollHeight};');

        WLogger::logDebug('Реальный размер элемента: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
