<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Conditions\Clickable;
use Codeception\Lib\WFramework\Conditions\Exist;
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
                ->should(new Exist())
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

            $autoClickViaJS = (bool) TestProperties::getValue('autoClickViaJS', false);

            $explanation = (new Clickable())->why($pageObject, false);

            if (!$autoClickViaJS)
            {
                WLogger::logError($this, $explanation->getMessage(), ['screenshot_blob' => $explanation->getScreenshot()]);

                throw $e;
            }

            WLogger::logWarning($this, $explanation->getMessage(), ['screenshot_blob' => $explanation->getScreenshot()]);

            WLogger::logDebug($this, 'autoClickViaJS = true -> пробуем кликнуть с помощью JS');

            $pageObject->accept(new MouseClickViaJS());
        }
    }
}
