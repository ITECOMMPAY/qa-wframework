<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetCssValues extends AbstractOperation
{
    /**
     * @var string[]
     */
    protected $property;

    /**
     * Возвращает значение CSS-свойств данного элемента.
     *
     * @param string ...$property
     */
    public function __construct(string ...$property)
    {
        $this->property = $property;
    }

    public function acceptWBlock($block) : array
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : array
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

    protected function apply(WPageObject $pageObject) : array
    {
        WLogger::logDebug('Получаем значение CSS-свойств: ' . implode(', ', $this->property));

        $result = [];

        foreach ($this->property as $property)
        {
            $result[] = $pageObject->accept(new GetCssValue($property));
        }

        WLogger::logDebug('CSS-свойства имеют значения: ' . implode(', ', $result));

        return $result;
    }
}
