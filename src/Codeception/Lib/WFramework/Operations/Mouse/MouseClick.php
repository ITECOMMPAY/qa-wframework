<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\Exception\WebDriverException;

class MouseClick extends AbstractOperation
{
    public function getName() : string
    {
        return "кликаем";
    }

    /**
     * Осуществляет клик на данном элементе.
     *
     * Если в настройках тестового модуля опция autoClickViaJS стоит в True, то первый клик на элементе будет
     * осуществляться посредством Селениума, и, если элемент окажется перекрыт, то будет произведён второй клик,
     * посредством JavaScript.
     */
    public function __construct() { }

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function acceptWCollection($collection)
    {
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        try
        {
            $pageObject
                ->returnSeleniumElement()
                ->click()
                ;
        }
        catch (WebDriverException $e)
        {
            if (strpos($e->getMessage(), 'is not clickable at point') === false)
            {
                throw $e;
            }

            $otherElement = '';

            if (preg_match('/Other element would receive the click: (?\'element\'.+) \(Session info/ms', $e->getMessage(), $matches) === 1)
            {
                $otherElement = $matches['element'];
            }

            WLogger::logWarning($this, 'Не получается кликнуть на элементе - он перекрыт другим элементом: ' . $otherElement);

            $autoClickViaJS = (bool) TestProperties::getValue('autoClickViaJS', False);

            if ($autoClickViaJS)
            {
                WLogger::logDebug($this, 'autoClickViaJS = true -> пробуем кликнуть с помощью JS');

                $pageObject->accept(new MouseClickViaJS());
                return;
            }
        }
    }
}
