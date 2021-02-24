<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetCssValues extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем значения CSS-свойств: " . implode(', ', $this->property);
    }

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
     * @return Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : array
    {
        $result = [];

        foreach ($this->property as $property)
        {
            $result[] = $pageObject->accept(new GetCssValue($property));
        }

        WLogger::logDebug($this, 'CSS-свойства имеют значения: ' . implode(', ', $result));

        return $result;
    }
}
