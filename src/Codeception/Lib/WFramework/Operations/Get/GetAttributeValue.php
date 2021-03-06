<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetAttributeValue extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем значение атрибута: $this->attribute";
    }

    /** @var string */
    protected $attribute;

    /**
     * Возвращает значение атрибута данного элемента или null, если такого атрибута нет.
     *
     * @param string $attribute - атрибут
     */
    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public function acceptWBlock($block) : ?string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : ?string
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

    protected function apply(WPageObject $pageObject) : ?string
    {
        $result = $pageObject
                        ->returnSeleniumElement()
                        ->getAttribute($this->attribute)
                        ;

        $resultText = json_encode($result);

        if (mb_strlen($resultText) > 64)
        {
            $resultText = substr($resultText, 0, 64) . ' ...';
        }

        WLogger::logDebug($this, 'Атрибут имеет значение: ' . $resultText);

        return $result;
    }
}
