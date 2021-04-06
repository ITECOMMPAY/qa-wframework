<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Color;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetBgColor extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем цвет фона";
    }

    /**
     * Возвращает цвет фона элемента.
     */
    public function __construct() {}

    public function acceptWBlock($block) : Color
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Color
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

    protected function apply(WPageObject $pageObject) : Color
    {
        $bgColor = $pageObject->accept(new GetCssValue('background-color'));

        $result = Color::fromString($bgColor);

        WLogger::logDebug($this, 'Получили цвет фона: ' . $result);

        return $result;
    }
}
