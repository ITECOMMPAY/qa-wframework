<?php


namespace Codeception\Lib\WFramework\WOperations\Field;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverKeys;

class FieldAppend extends AbstractOperation
{
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
            ->getProxyWebElement()
            ->sendKeys([WebDriverKeys::CONTROL, WebDriverKeys::END])
            ->sendKeys($this->value)
            ;
    }
}
