<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;
use Facebook\WebDriver\WebDriverPoint;

class GetLocation extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем координаты (верхний левый угол)";
    }

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
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : WebDriverPoint
    {
        $result = $pageObject
                        ->returnSeleniumElement()
                        ->getLocation()
                        ;

        WLogger::logDebug($this, sprintf("Координаты элемента: x:%d , y:%d", $result->getX(), $result->getY()));

        return $result;
    }
}
