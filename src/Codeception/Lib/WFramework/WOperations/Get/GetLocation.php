<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverPoint;

class GetLocation extends AbstractOperation
{
    /**
     * Возвращает координаты элемента (левый верхний угол)
     */
    public function __construct() {}

    public function acceptWBlock($block) : WebDriverPoint
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : WebDriverPoint
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

    protected function apply(WPageObject $pageObject) : WebDriverPoint
    {
        WLogger::logDebug('Получаем координаты элемента (левый верхний угол)');

        $result = $pageObject
                        ->getProxyWebElement()
                        ->getLocation()
                        ;

        WLogger::logDebug(sprintf("Координаты элемента: x:%d , y:%d", $result->getX(), $result->getY()));

        return $result;
    }
}
