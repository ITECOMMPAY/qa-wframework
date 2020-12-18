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

class GetBorderColor extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем цвет обводки";
    }

    /**
     * Возвращает цвет обводки элемента
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
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : Color
    {
        WLogger::logDebug('Получаем цвет обводки элемента');

        $borderColor = $pageObject
                            ->returnSeleniumElement()
                            ->getCSSValue('border-top-color')
                            ;

        $result = Color::fromString($borderColor);

        WLogger::logDebug('Получили цвет обводки: ' . $result);

        return $result;
    }
}
