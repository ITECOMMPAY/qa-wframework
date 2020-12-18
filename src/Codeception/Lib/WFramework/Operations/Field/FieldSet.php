<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class FieldSet extends AbstractOperation
{
    public function getName() : string
    {
        return "задаём полю ввода текст: $this->value";
    }

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
            ->returnSeleniumElement()
            ->clear()
            ->sendKeys($this->value)
            ;
    }
}
