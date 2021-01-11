<?php


namespace Codeception\Lib\WFramework\WebObjects\Base\Traits;


use Codeception\Actor;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WLocator\HtmlRoot;
use Codeception\Lib\WFramework\WLocator\WLocator;

trait BlockBaseMethods
{
    /**
     * Данный метод должен возвращать конкретное имя данного PageObject'а
     *
     * Например: 'Страница сброса пароля', 'Панель выбранных фильтров'
     *
     * Он нужен потому что из одного имени класса не всегда понятно - что это за PageObject и зачем он нужен.
     *
     * @return string
     */
    abstract protected function initName() : string;

    /**
     * Данный метод должен описывать - как с первой страницы дойти до данного PageObject'а.
     *
     * Он будет использоваться в смок-тесте, проверяющем актуальность всех локаторов.
     *
     * Он не должен проверять в конце что PageObject отобразился - этим занимается метод display()
     *
     * Если необходимо отобразить данный PageObject, то вместо данного метода следует
     * использовать метод display(), который не только вызовет данный метод, но и
     * сделает запись в логе, а так же проверит, что PageObject отобразился.
     *
     * @return mixed
     */
    abstract protected function openPage();

    /**
     * Переходит с первой страницы сайта (обычно страница логина) до данного PageObject'а, используя метод openPage().
     *
     * Проверяет, что PageObject успешно отобразился.
     *
     * @return $this
     */
    public function display()
    {
        WLogger::logInfo("Открываем страницу: " . $this);

        $this->openPage();

        $this->shouldExist();

        return $this;
    }

    /**
     * Этот метод возвращает локатор, относительно которого будет производиться поиск всех веб-элементов на данном
     * PageObject'е. По умолчанию, данный метод возвращает корень страницы (/html).
     *
     * Для использования в PageObject'ах - его следует переопределить.
     *
     * Метод может вернуть экземпляр WLocator или строку. Строка, по-умолчанию, считается за XPath.
     *
     * @return WLocator|string
     */
    protected function initPageLocator()
    {
        return HtmlRoot::get();
    }

    protected function initBlock(Actor $actor)
    {
        if (!method_exists($actor, 'getWebDriver'))
        {
            throw new \Exception('Для актора не подключен модуль Common\WebFramework\WebModule\WebModule');
        }

        $this->name = $this->initName();
        $this->actor = $actor;
        $this->proxyWebDriver = $actor->getWebDriver();
        $this->relative = false;

        if ($this->initPageLocator() instanceof WLocator)
        {
            $this->locator = $this->initPageLocator();
        }
        else
        {
            $this->locator = WLocator::xpath($this->initPageLocator()); //Локаторы заданные строкой считаются за Xpath-локаторы
        }
    }

    public function __toString() : string
    {
        return $this->getClassShort() . ' (' . $this->getName() . ')';
    }
}