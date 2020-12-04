<?php


namespace Codeception\Lib\WFramework\WOperations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\Exception\WebDriverException;

class MouseClick extends AbstractOperation
{
    /**
     * Осуществляет клик на данном элементе.
     *
     * Если в настройках тестового модуля опция clickViaJS стоит в True, то клик на элементе будет осуществляться
     * посредством JavaScript. Такой клик игнорирует перекрытие данного элемента другим элементом.
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
        $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Кликаем на элементе');

        $element = $pageObject->getProxyWebElement();

        $clickViaJs = (bool) TestProperties::getValue('clickViaJS');

        if ($clickViaJs)
        {
            $pageObject->accept(new MouseClickViaJS());
            return;
        }

        try
        {
            $element->click();
        }
        catch (WebDriverException $e)
        {
            if (strpos($e->getMessage(), 'is not clickable at point') === False)
            {
                throw $e;
            }

            $otherElement = '';

            if (preg_match('/Other element would receive the click: (?\'element\'.+) \(Session info/ms', $e->getMessage(), $matches) === 1)
            {
                $otherElement = $matches['element'];
            }

            WLogger::logWarning('Не получается кликнуть на элементе - он перекрыт другим элементом: ' . $otherElement);

            $autoClickViaJS = (bool) TestProperties::getValue('autoClickViaJS', False);

            if ($autoClickViaJS)
            {
                WLogger::logDebug('autoClickViaJS == True -> пробуем кликнуть с помощью JS');

                $pageObject->accept(new MouseClickViaJS());
                return;
            }
        }
    }
}
