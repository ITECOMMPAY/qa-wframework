<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;
use Facebook\WebDriver\WebDriverDimension;

class GetClientSize extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем внутренний размер без границ и полос прокруток (clientWidth x clientHeight)";
    }

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
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : WebDriverDimension
    {
        $size = $pageObject->accept(new ExecuteScriptOnThis('return {"width": arguments[0].clientWidth, "height": arguments[0].clientHeight};'));

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
