<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetAttribute extends AbstractOperation
{
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : ?string
    {
        WLogger::logDebug('Получаем значение атрибута: ' . $this->attribute);

        $result = $pageObject
                        ->getProxyWebElement()
                        ->getAttribute($this->attribute)
                        ;

        WLogger::logDebug('Атрибут имеет значение: ' . json_encode($result));

        return $result;
    }
}
