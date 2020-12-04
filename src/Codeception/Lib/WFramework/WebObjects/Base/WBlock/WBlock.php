<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 16:06
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WBlock;


use Codeception\Actor;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\HtmlRoot;
use Codeception\Lib\WFramework\WLocator\WLocator;

/**
 * WBlock - это одна из разновидностей PageObject'а.
 *
 * Наследники WBlock описывают конкретный блок страницы. Из-за этого они всегда существуют в единственном экземпляре.
 *
 * WBlock состоят из веб-элементов (наследников WElement). Локаторы веб-элементов ищутся относительно локатора WBlock.
 * Локатор WBlock по умолчанию - корень страницы (/html).
 *
 * WBlock не содержат внутри себя сложной логики - вся их логика лежит снаружи в тестах, а WBlock просто дают тестам
 * прямой доступ к своим веб-элементам.
 *
 * Для удобства наследники WBlock в фреймворке называются PageObject'ами. А наследники WElement - веб-элементами.
 * Хотя, на самом деле, они оба являются разновидностями PageObject'ов.
 *
 * В тестах, сначала нужно создать общий класс, который наследует от данного класса и переопределяет его конструктор
 * с указанием конкретного Актора (Gate2025Tester, PPTester), вместо абстрактного Codeception\Actor, и затем наследовать
 * все PageObject'ы от этого общего класса. Иначе Codeception не сможет их создать.
 *
 * @package Common\Module\WFramework\WebObjects\Base
 */
abstract class WBlock extends WPageObject
{
    /**
     * WBlock всегда задаются относительно корня страницы и не могут быть объявлены внутри других PageObject'ов.
     *
     * Поэтому параметр $relative для них всегда стоит в false.
     * @var bool
     */
    protected $relative = false;

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

    /**
     * Конструктор PageObject'а.
     *
     * В тестах, сначала нужно создать общий класс, который наследует от класса WBlock и переопределяет этот конструктор
     * с указанием конкретного Актора (Gate2025Tester, PPTester), вместо абстрактного Codeception\Actor, и затем наследовать
     * все PageObject'ы от этого общего класса. Иначе Codeception не сможет их создать.
     *
     * Например, так:
     *
     *      abstract class BofBlock extends WBlock
     *      {
     *          public function __construct(BackofficeTester $actor)
     *          {
     *              parent::__construct($actor);
     *          }
     *      }
     *
     * Не стоит забывать, что если в конструкторе PageObject'а прописываются поля, то данный родительский конструктор
     * следует вызывать в самом конце, чтобы он прописал эти поля, как элементы PageObject'а.
     *
     * Например:
     *
     *      public function __construct(BackofficeTester $actor)
     *      {
     *          $this->emailField =     WTextBox::fromLocator('Email',    WLocator::xpath(".//input[@name='email']"));
     *          $this->passwordField =  WTextBox::fromLocator('Password', WLocator::xpath(".//input[@name='password']"));
     *
     *          $this->loginButton =    WButton::fromLocator('Login',     WLocator::xpath(".//button[contains(., 'Login')]"));
     *
     *          parent::__construct($actor);
     *      }
     *
     * @param Actor $actor
     * @throws \Exception
     */
    public function __construct(Actor $actor)
    {
        parent::__construct();

        $this->name = $this->initName();

        if (!method_exists($actor, 'getWebDriver'))
        {
            throw new \Exception('Для актора не подключен модуль Common\WebFramework\WebModule\WebModule');
        }

        $this->actor = $actor;
        $this->webDriver = $actor->getWebDriver();

        if ($this->initPageLocator() instanceof WLocator)
        {
            $this->locator = $this->initPageLocator();
        }
        else
        {
            $this->locator = WLocator::xpath($this->initPageLocator());
        }
    }

    public function __toString() : string
    {
        return $this->getClassShort() . ' (' . $this->getName() . ')';
    }
}
