<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Operations\Get\GetAttributeValue;
use Codeception\Lib\WFramework\Operations\Get\GetTagName;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;

class MouseClickSmart extends AbstractOperation
{
    public function getName() : string
    {
        return "кликаем (smart)";
    }

    /**
     * Осуществляет умный клик на данном элементе.
     *
     * Если в настройках тестового модуля опция clickViaJS стоит в True, то клик на элементе будет осуществляться
     * посредством JavaScript. Такой клик игнорирует перекрытие данного элемента другим элементом.
     *
     * Если элемент является svg - то клик на нём будет осуществлён посредством JavaScript т.к. Selenium не умеет
     * кликать на таких элементах.
     *
     * Если в настройках тестового модуля опция clickOnHref стоит в False, и элемент является ссылкой - то вместо клика
     * по ней будет осуществлён переход с помощью WebDriver get().
     *
     * Если ребёнок данного элемента является ссылкой, то клик будет осуществлён по ребёнку.
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
        $clickViaJs = (bool) TestProperties::getValue('clickViaJS');

        if ($clickViaJs)
        {
            WLogger::logDebug("clickViaJS = true, значит кликаем с помощью JavaScript.");

            $pageObject->accept(new MouseClickViaJS());
            return;
        }

        if ($pageObject->accept(new GetTagName()) === 'svg')
        {
            WLogger::logDebug("Кнопка является SVG. Единственный способ по ней кликнуть - с помощью JavaScript.");

            //https://stackoverflow.com/questions/14592213/selenium-webdriver-clicking-on-elements-within-an-svg-using-xpath
            $pageObject->accept(new MouseClickViaJS());

            return;
        }

        $clickOnHref = (bool) TestProperties::getValue('clickOnHref');

        if (!$clickOnHref)
        {
            $href = $pageObject->accept(new GetAttributeValue('href'));

            if ($href !== null)
            {
                WLogger::logDebug("Кнопка является ссылкой: '$href' - и clickOnHref = false, значит пробуем по ней перейти.");

                $pageObject
                    ->returnCodeceptionActor()
                    ->amOnUrl($href)
                    ;

                return;
            }
        }

        $innerHtml = $pageObject->accept(new GetAttributeValue('innerHTML'));

        if (preg_match('%<a\s+\X*\s+href=\X*<\/a>%iUu', $innerHtml, $matches))
        {
            /**
             * При работе с коллекцией веб-элементов, типа таблицы мы обращаемся к её ячейкам, как к /div.
             * В то же время, некоторые ячейки таблицы могут содержать в себе кнопки. Но не /div/button, а /div/a.
             * Попытка кликнуть на div может привести к двоякому результату. Клик осуществляется по центру элемента.
             * Если текст ссылки короткий, выравнен по краю и не доходит до центра элемента - клик промахнётся.
             * Поэтому в такой ситуации мы должны кликать не по div, а по a.
             */
            WLogger::logDebug('Кнопка является ссылкой, но локатор данного элемента не указывает на ссылку. Пробуем кликнуть по ссылке.');

            /** @var WPageObject $poClass */
            $poClass = get_class($pageObject);

            /** @var WPageObject $button */
            $button = $poClass::fromLocator('//a', WLocator::xpath('.//a'));
            $button->setParent($pageObject);

            $button->accept(new MouseClickSmart());

            return;
        }

        $pageObject->accept(new MouseScrollTo());
        $pageObject->accept(new MouseClick());
    }
}
