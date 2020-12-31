<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetCssValue extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем значение CSS-свойства: $this->property";
    }

    /**
     * @var string
     */
    protected $property;

    /**
     * Возвращает значение CSS-свойства данного элемента.
     *
     * @param string $property - CSS-свойство
     */
    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
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

    protected function apply(WPageObject $pageObject) : string
    {
        $result = $pageObject
                        ->returnSeleniumElement()
                        ->getCSSValue($this->property)
                        ;

        WLogger::logDebug('CSS-свойство имеет значение: ' . $result);

        return $result;
    }
}
