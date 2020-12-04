<?php


namespace Codeception\Lib\WFramework\WOperations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class FieldSet extends AbstractOperation
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Задаёт текст данного элемента (через sendKeys).
     *
     * Если элемент содержал текст - он будет заменён.
     *
     * @param string $value - новый текст для элемента
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Задаём значение: ' . $this->value);

        $pageObject
            ->getProxyWebElement()
            ->clear()
            ->sendKeys($this->value)
            ;
    }
}
