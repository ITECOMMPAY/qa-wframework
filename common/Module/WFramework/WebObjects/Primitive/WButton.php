<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 17:23
 */

namespace Common\Module\WFramework\WebObjects\Primitive;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IClickable;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IHaveReadableText;
use Common\Module\WFramework\WLocator\WLocator;

class WButton extends WElement implements IClickable, IHaveReadableText
{
    protected function initTypeName() : string
    {
        return 'Кнопка';
    }

    /**
     * Кликает на элементе
     *
     * Если параметр фреймворка clickOnHref стоит в true и данный элемент содержит ссылку (a href), то данный метод
     * попробует кликнуть по этой ссылке.
     *
     * Если параметр фреймворка clickOnHref стоит в false и данный элемент содержит ссылку (a href), то данный метод
     * перейдёт по ссылке с помощью метода amOnUrl(). Если на ссылку повешен JS код который срабатывает по клику то
     * он скорее-всего не отработает.
     *
     * Если параметр фреймворка clickViaJS стоит в true, то клик на элементе будет осуществляться
     * посредством JavaScript. Такой клик игнорирует перекрытие данного элемента другим элементом.
     *
     * Если параметр фреймворка autoClickViaJS стоит в true, то первый клик на элементе будет
     * осуществляться посредством Селениума, и, если элемент окажется перекрыт, то будет произведён второй клик,
     * посредством JavaScript.
     *
     * По умолчанию:
     *      clickOnHref = true;
     *      clickViaJS = false;
     *      autoClickViaJS = false;
     *
     * @return $this
     * @throws \Common\Module\WFramework\Exceptions\Common\UsageException
     * @throws \Facebook\WebDriver\Exception\UnknownServerException
     * @throws \Facebook\WebDriver\Exception\WebDriverException
     */
    public function click() : WButton
    {
        WLogger::logInfo($this . " -> кликаем");

        if ($this->returnSeleniumElement()->get()->tag() === 'svg')
        {
            WLogger::logDebug("Кнопка является SVG. Единственный способ по ней кликнуть - с помощью JavaScript.");

            //https://stackoverflow.com/questions/14592213/selenium-webdriver-clicking-on-elements-within-an-svg-using-xpath

            $this->returnSeleniumElement()->exec()->scriptOnThis("arguments[0].dispatchEvent(new MouseEvent('click', {view: window, bubbles:true, cancelable: true}))");

            return $this;
        }

        $clickOnHref = (bool) TestProperties::getValue('clickOnHref');

        if (!$clickOnHref)
        {
            $href = $this
                        ->returnSeleniumElement()
                        ->get()
                        ->attribute('href')
                        ;

            if ($href !== null)
            {
                WLogger::logDebug("Кнопка является ссылкой: '$href' - и clickOnHref = false, значит пробуем по ней перейти.");

                $this
                    ->returnCodeceptionActor()
                    ->amOnUrl($href)
                    ;

                return $this;
            }
        }

        $innerHtml = $this->returnSeleniumElement()->get()->attribute('innerHTML');

        $matchResult = preg_match_all('%<a\s+\X*\s+href=\X*<\/a>%iUu', $innerHtml, $matches);

        if ($matchResult === 1)
        {
            /**
             * При работе с коллекцией веб-элементов, типа таблицы мы обращаемся к её ячейкам, как к /div.
             * В то же время, некоторые ячейки таблицы могут содержать в себе кнопки. Но не /div/button, а /div/a.
             * Попытка кликнуть на div может привести к двоякому результату. Клик осуществляется по центру элемента.
             * Если текст ссылки короткий, выравнен по краю и не доходит до центра элемента - клик промахнётся.
             * Поэтому в такой ситуации мы должны кликать не по div, а по a.
             */
            WLogger::logDebug('Кнопка является ссылкой и локатор данного элемента не указывает на ссылку. Пробуем кликнуть по ссылке.');

            /** @var WButton $aButton */
            $aButton = WButton::fromLocator('//a', WLocator::xpath('.//a'));
            $aButton->setParent($this);

            $aButton
                ->returnSeleniumElement()
                ->mouse()
                ->scrollTo()
                ->click()
                ;

            return $this;
        }

        $this
            ->returnSeleniumElement()
            ->mouse()
            ->scrollTo()
            ->click()
            ;

        return $this;
    }

    /**
     * У некоторых элементов методы повешены не на click, а на mousedown ивент.
     * По-хорошему Селениум должен триггерить оба ивента, но у нас, возможно из-за древнего селениум-сервера,
     * при обычном клике пускается только click ивент, а при клике через Actions - только mousedown/mouseup ивенты.
     *
     * Так что для случаев когда click() не срабатывает - сделан этот метод, который кликает на элементе с помощью
     * Actions.
     *
     * Автоматизировать выбор - каким способом кликать на элементе, к сожалению, не получиться т.к. в JavaScript
     * нет способа получить список - на какие ивенты повешены обработчики у элемента.
     *
     * @return WButton
     * @throws \Common\Module\WFramework\Exceptions\Common\UsageException
     */
    public function clickMouseDown() : WButton
    {
        $this
            ->returnSeleniumElement()
            ->mouse()
            ->scrollTo()
            ->clickWithLeftButton()
            ;

        return $this;
    }

    /**
     * Возвращает видимый текст элемента, отфильтрованный по регулярке
     */
    public function getFilteredText(string $regex, string $groupName = '') : string
    {
        WLogger::logInfo($this . " -> получаем значение отфильтрованное по регулярке: $regex");

        return $this
                    ->returnSeleniumElement()
                    ->get()
                    ->filteredText($regex, $groupName)
                    ;
    }
}
