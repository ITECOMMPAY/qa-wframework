<?php


namespace Codeception\Lib\WFramework\Operations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldAppend extends AbstractOperation
{
    public function getName() : string
    {
        return "дописываем в конец поля ввода: $this->value";
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * Добавляет текст в конец имеющегося текста элемента (Ctrl+End, send keys).
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
        WLogger::logDebug('Добавляем значение в конец поля: ' . $this->value);

        $pageObject
            ->returnSeleniumElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::END])
            ->sendKeys($this->value)
            ;
    }
}
