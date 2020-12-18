<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldPrepend extends AbstractOperation
{
    public function getName() : string
    {
        return "дописываем в начало поля ввода: $this->value";
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * Добавляет текст в начало имеющегося текста элемента (Ctrl+Home, send keys).
     *
     * @param string $value - текст, который следует добавить
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
        WLogger::logDebug('Добавляем значение в начало поля: ' . $this->value);

        $pageObject
            ->returnSeleniumElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::HOME])
            ->sendKeys($this->value)
            ;
    }
}
