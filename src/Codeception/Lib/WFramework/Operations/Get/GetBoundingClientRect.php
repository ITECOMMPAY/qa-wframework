<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetBoundingClientRect extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем boundingClientRect";
    }

    /**
     * Возвращает boundingClientRect элемента
     */
    public function __construct() {}

    public function acceptWBlock($block) : Rect
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Rect
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

    protected function apply(WPageObject $pageObject) : Rect
    {
        WLogger::logDebug('Получаем boundingClientRect элемента');

        $rect = Rect::fromDOMRect($pageObject->returnSeleniumElement()->executeScriptOnThis('return arguments[0].getBoundingClientRect();'));

        WLogger::logDebug('Получили boundingClientRect элемента: ' . $rect);

        return $rect;
    }
}
