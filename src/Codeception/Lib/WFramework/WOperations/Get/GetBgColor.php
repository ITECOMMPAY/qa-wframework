<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Helpers\Color;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetBgColor extends AbstractOperation
{
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : Color
    {
        WLogger::logDebug('Получаем цвет фона элемента');

        $bgColor = $pageObject->accept(new GetCssValue('background-color'));

        $result = Color::fromString($bgColor);

        WLogger::logDebug('Получили цвет фона: ' . $result);

        return $result;
    }
}
