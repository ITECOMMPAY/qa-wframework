<?php


namespace Codeception\Lib\WFramework\WebObjects\Base;


use Codeception\Actor;
use Codeception\Lib\WFramework\Actor\ImaginaryActor;
use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\Debug\DebugHelper;
use Codeception\Lib\WFramework\Debug\DebugInfo;
use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Exceptions\FacadeWebElementOperations\WaitUntilElement;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;
use Codeception\Lib\WFramework\Helpers\Composite;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\ProxyWebElement\ProxyWebElement;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\ComparisonResult\Diff;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\ComparisonResult\Same;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\SelfieShooter;
use Codeception\Lib\WFramework\WLocator\EmptyLocator;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use function microtime;
use function usleep;

/**
 * Класс-предок для всех PageObject'ов
 *
 * Содержит общие реализации всех методов описанных в интерфейсе IPageObject
 *
 * @package Common\Module\WFramework\WebObjects\Base
 */
abstract class WPageObject extends Composite implements IPageObject
{
    /**
     * Все PageObject'ы привязаны к DOM-дереву через локатор.
     *
     * @var WLocator
     */
    protected $locator = null;

    /**
     * Все PageObject'ы имеют ссылку на главного актора Codeception, чтобы дёргать его методы
     *
     * @var ImaginaryActor
     */
    protected $actor = null;

    /**
     * Все PageObject'ы имеют ссылку на Сервер Селениума, чтобы дёргать его методы
     *
     * @var RemoteWebDriver
     */
    protected $webDriver = null;

    /**
     * Все PageObject'ы имеют ссылку на свой элемент Селениума, чтобы дёргать его методы
     *
     * Элемент Селениума обёрнут в красивый фасад
     *
     * @var  FacadeWebElement
     */
    protected $facadeWebElement = null;

    /**
     * Все PageObject'ы могут являться частью других PageObject'ов.
     *
     * PageObject в котором объявлены другие PageObject'ы - считается их родителем. Если родитель описывает блок страницы,
     * то его дети описывают куски этого блока, а их дети - кусочки этих кусков.
     *
     * PageObject'ы дети ищут себя относительно локатора своего родителя.
     *
     * @var bool
     */
    protected $relative = true;

    /**
     * @var SelfieShooter|null
     */
    protected $selfieShooter = null;

    public function __construct()
    {
        parent::__construct();

        $this->locator = EmptyLocator::get();
    }

    /**
     * Возвращает локатор данного элемента
     *
     * @return WLocator
     */
    public function getLocator() : WLocator
    {
        return $this->locator;
    }

    /**
     * Определён ли локатор данного веб-элемента относительно его родителя (другого PageObject'а)?
     *
     * @return bool
     */
    public function isRelative() : bool
    {
        return $this->relative;
    }

    /**
     * С помощью этого метода можно обратиться к методам элемента Селениума, который лежит под капотом данного PageObject'а
     *
     * Элемент Селениума обёрнут в FacadeWebElement, который предоставляет удобный интерфейс для работы с ним, а так же
     * место для хранения всех самописных низкоуровневых методов, типа методов для получения значений атрибутов и CSS-свойств.
     *
     * Обычно все наружные методы PageObject'а реализованы через вызовы FacadeWebElement.
     *
     * @return FacadeWebElement
     * @throws UsageException
     */
    public function returnSeleniumElement() : FacadeWebElement
    {
        if ($this->facadeWebElement === null)
        {
            if ($this->relative === True)
            {
                $this->facadeWebElement = FacadeWebElement::fromLocator($this->locator, $this->returnSeleniumServer(), $this->getParent()->returnSeleniumElement());
            }
            else
            {
                $this->facadeWebElement = FacadeWebElement::fromLocator($this->locator, $this->returnSeleniumServer());
            }
        }

        if (!$this->facadeWebElement->returnProxyWebElement()->hasDebugInfo())
        {
            $debugInfo = (new DebugInfo())->setPageObject($this);
            $this->facadeWebElement->returnProxyWebElement()->setDebugInfo($debugInfo);
        }

        return $this->facadeWebElement;
    }

    /**
     * С помощью этого метода можно обратиться к методам главного актора Codeception
     *
     * @return Actor|ImaginaryActor
     * @throws UsageException
     */
    public function returnCodeceptionActor()
    {
        if ($this->actor === null)
        {
            if ($this->getParent() instanceof EmptyComposite)
            {
                if ($this instanceof WBlock)
                {
                    throw new UsageException($this . ' -> не содержит ссылку на актора. Это странно, учитвая, чтоа ктор должен передаваться в её конструкторе');
                }

                if ($this instanceof WElement)
                {
                    throw new UsageException($this . ' -> должен располагаться на WBlock.');
                }

                throw new UsageException($this . ' -> не содержит актора и не является WBlock и WElement - ');
            }

            $this->actor = $this->getParent()->returnCodeceptionActor();
        }

        return $this->actor;
    }

    /**
     * С помощью этого метода можно обратиться к методам Сервера Селениума
     *
     * @return RemoteWebDriver
     * @throws UsageException
     */
    public function returnSeleniumServer() : RemoteWebDriver
    {
        if ($this->webDriver === null)
        {
            if ($this->getParent() instanceof EmptyComposite)
            {
                if ($this instanceof WBlock)
                {
                    throw new UsageException($this . ' -> не содержит ссылку на webDriver. Это странно, учитывая, что актор должен передаваться в конструкторе WBlock');
                }

                if ($this instanceof WElement)
                {
                    throw new UsageException($this . ' -> должен располагаться на WBlock.');
                }

                throw new UsageException($this . ' -> не содержит webDriver и не является WBlock и WElement');
            }

            $this->webDriver = $this->getParent()->returnSeleniumServer();
        }

        return $this->webDriver;
    }

    /**
     * С помощью этого метода можно обратиться к методам Selenium Actions
     *
     * https://selenium.dev/selenium/docs/api/java/org/openqa/selenium/interactions/Actions.html
     *
     * @return WebDriverActions
     * @throws UsageException
     */
    public function returnSeleniumActions() : WebDriverActions
    {
        return new WebDriverActions($this->returnSeleniumServer());
    }

    public function returnSelfieShooter() : SelfieShooter
    {
        if ($this->selfieShooter === null)
        {
            $this->selfieShooter = new SelfieShooter($this);
        }

        return $this->selfieShooter;
    }

    /**
     * Метод валит тест
     *
     * @param string $description - причина
     * @throws UsageException
     */
    protected function fail(string $description = '')
    {
        $this
            ->returnCodeceptionActor()
            ->fail($this . PHP_EOL . ' -> ' . $description)
        ;
    }

    /**
     * Ждёт выполнение заданного условия для данного PageObject'а.
     *
     * Если условие не было выполнено в течении заданного таймаута (elementTimeout для наследников WElement,
     * collectionTimeout для наследников WCollection) - валит тест.
     *
     * @param Cond $condition - условие
     * @param string $description - описание причины, по которой заданное условие должно выполняться для данного PageObject'а
     * @param callable|null $debugHandler - опциональная функция, которая продиагностирует почему условие не выполнилось
     *                                      и сообщит тестировщику в удобном для понимания виде
     * @return $this
     * @throws UsageException
     */
    protected function should(Cond $condition, string $description = '', callable $debugHandler = null)
    {
        WLogger::logInfo($this . ' -> ' . $description);

        try
        {
            $this
                ->returnSeleniumElement()
                ->wait()
                ->until($condition)
                ;
        }
        catch (WaitUntilElement $e)
        {
            $message = $condition->toString();

            if ($debugHandler !== null)
            {
                $debugInfo = (new DebugInfo())->setPageObject($this);
                $message .= PHP_EOL . $debugHandler($debugInfo);
            }

            $this->fail($message);
        }

        return $this;
    }

    /**
     * Вызывает should-метод для всех PageObject'ов, которые объявлены внутри данного PageObject'а
     *
     * @param string $shouldMethod
     * @return $this
     */
    protected function eachChildShould(string $shouldMethod)
    {
        foreach ($this->getChildren() as $child)
        {
            $child->$shouldMethod();
        }

        return $this;
    }

    public function shouldExist(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::exist(), 'должен существовать', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST);});
        }

        if ($deep)
        {
            $this->eachChildShould('shouldExist');
        }

        return $this;
    }

    public function shouldNotExist(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::not(Cond::exist()), 'НЕ должен существовать', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::NOT_EXIST);});
        }

        elseif ($deep)
        {
            $this->eachChildShould('shouldNotExist');
        }

        return $this;
    }

    public function shouldBeDisplayed(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::visible(), 'должен отображаться', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST, DebugHelper::VISIBLE);});
        }

        if ($deep)
        {
            $this->eachChildShould('shouldBeDisplayed');
        }

        return $this;
    }

    public function shouldBeHidden(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::hidden(), 'НЕ должен отображаться', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::HIDDEN);});
        }

        elseif ($deep)
        {
            $this->eachChildShould('shouldBeHidden');
        }

        return $this;
    }

    public function shouldBeEnabled(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::enabled(), 'должен быть доступен', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST, DebugHelper::ENABLED);});
        }

        if ($deep)
        {
            $this->eachChildShould('shouldBeEnabled');
        }

        return $this;
    }

    public function shouldBeDisabled(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::disabled(), 'должен быть недоступен', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST, DebugHelper::DISABLED);});
        }

        elseif ($deep)
        {
            $this->eachChildShould('shouldBeDisabled');
        }

        return $this;
    }

    public function shouldBeInViewport(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::inView(), 'должен быть внутри рамок окна', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST, DebugHelper::IN_VIEWPORT);});
        }

        if ($deep)
        {
            $this->eachChildShould('shouldBeInViewport');
        }

        return $this;
    }

    public function shouldBeOutOfViewport(bool $deep = true)
    {
        if (!$this->getLocator()->isHtmlRoot())
        {
            $this->should(Cond::not(Cond::inView()), 'должен быть за рамками окна', function (DebugInfo $debugInfo){return (new DebugHelper())->diagnoseLocator($debugInfo, DebugHelper::EXIST, DebugHelper::OUT_OF_VIEWPORT);});
        }

        if ($deep)
        {
            $this->eachChildShould('shouldBeOutOfViewport');
        }

        return $this;
    }

    public function shouldContainText(string $text)
    {
        return $this->should(Cond::textThatContains($text), "должен содержать текст: $text");
    }

    protected function is(Cond $condition, string $description) : bool
    {
        WLogger::logInfo($this . ' -> ' . $description);

        return $this
                    ->returnSeleniumElement()
                    ->checkIt()
                    ->is($condition)
                    ;
    }

    protected function eachChildIs(string $isMethod) : bool
    {
        foreach ($this->getChildren() as $child)
        {
            if (!$child->$isMethod())
            {
                return false;
            }
        }

        return true;
    }

    public function isExist(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::exist(), 'существует?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isExist');
        }

        return true;
    }

    public function isNotExist(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::not(Cond::exist()), 'НЕ существует?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isNotExist');
        }

        return true;
    }

    public function isDisplayed(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::visible(), 'отображается?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isDisplayed');
        }

        return true;
    }

    public function isHidden(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::hidden(), 'НЕ отображается?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isHidden');
        }

        return true;
    }

    public function isEnabled(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::enabled(), 'доступен?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isEnabled');
        }

        return true;
    }

    public function isDisabled(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::disabled(), 'НЕ доступен?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isEnabled');
        }

        return true;
    }

    public function isInViewport(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::inView(), 'внутри рамок окна?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isInViewport');
        }

        return true;
    }

    public function isOutOfViewport(bool $deep = true) : bool
    {
        if (!$this->getLocator()->isHtmlRoot() && !$this->is(Cond::not(Cond::inView()), 'за рамками окна?'))
        {
            return false;
        }

        if ($deep)
        {
            return $this->eachChildIs('isOutOfViewport');
        }

        return true;
    }

    public function isContainingText(string $text) : bool
    {
        return $this->is(Cond::textThatContains($text), "содержит текст: '$text'?");
    }

    /**
     * Возвращает видимый текст PageObject'а
     *
     * В корне этого метода лежит дефолтный метод Селениума: getText()
     *
     * @return string
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     * @throws UsageException
     */
    public function getVisibleText() : string
    {
        WLogger::logInfo($this . " -> получаем видимый текст");

        $result = $this
                    ->returnSeleniumElement()
                    ->get()
                    ->text()
                    ;

        WLogger::logInfo($this . " -> имеет видимый текст: '$result'");

        return $result;
    }

    /**
     * Возвращает весь текст PageObject'а (включая невидимый)
     *
     * Этот метод получает текст элемента с помощью JavaScript и работает медленнее чем getVisibleText().
     *
     * Если элемент не содержит текст, но у него задан value, то будет возвращено значение value.
     *
     * @return string
     * @throws UsageException
     */
    public function getAllText() : string
    {
        WLogger::logInfo($this . " -> получаем весь текст (включая невидимый)");

        $result = $this
                    ->returnSeleniumElement()
                    ->get()
                    ->rawText()
                    ;

        WLogger::logInfo($this . " -> имеет весь текст: '$result'");

        return $result;
    }

    /**
     * Скроллит экран к элементу
     *
     * @return $this
     * @throws UsageException
     */
    public function scrollTo()
    {
        WLogger::logInfo($this . " -> скроллим к элементу");

        $this
            ->returnSeleniumElement()
            ->mouse()
            ->scrollTo()
            ;

        return $this;
    }

    /**
     * PageObject должен выглядеть, как сохранённый эталон.
     *
     * Если условие не будет выполнено в течении заданного таймаута (elementTimeout) будет мягкий фейл.
     * Т.е. в самом конце прогона теста, когда будет вызван $I->assertAll() - тест зафейлится.
     *
     * В зависимости от значения параметра фреймворка "shotRun" данный метод ведёт себя по-разному.
     * Если shotRun == true, то метод просто сохраняет скриншот в каталог временных скриншотов (_data/shots/temp)
     * и затем ждёт $defaultDelay, чтобы скорость выполнения теста не отличалась от обычной.
     * Если shotRun == false, то метод сверяет текущий скриншот с эталоном.
     *
     * Параметр фреймворка "maxDeviation" задаёт максимальную допустимую погрешность для признания скриншотов одинаковыми.
     * Где 0 означает, что скриншоты совпадают полностью.
     *
     * Параметр "source" модуля ShotsStorageModule задаёт - откуда будет браться эталон.
     * Если source == local, то эталон будет браться из тестового каталога _data/shots.
     * Если source == remote, то эталон будет браться с S3, если его пока нет в _data/shots.
     *
     * @param string $suffix - если у PageObject'а есть несколько различных состояний, то скриншот для конкретного
     *                         состояния можно пометить с помощью суффикса. Желательно чтобы суффикс умещался в 8 символов.
     * @param int $defaultDelay
     * @param null $waitClosure - функция которую нужно вызвать после прокрутки элемента, например функция,
     *                            которая ждёт исчезновения ждуна
     * @return $this
     * @throws UsageException
     * @throws \ImagickException
     */
    public function shouldBeLikeBefore(string $suffix = 'default', int $defaultDelay = 250000, $waitClosure = null)
    {
        WLogger::logInfo($this . ' -> должен выглядеть, как сохранённый эталон: ' . $suffix);

        try
        {
            $this
                ->returnSeleniumElement()
                ->wait()
                ->until(Cond::pageLoaded())
                ;
        }
        catch (WaitUntilElement $e) {}

        $screenshot = $this->returnSelfieShooter()->takeScreenshot('', $waitClosure);

        $shotRun = (bool) TestProperties::getValue('shotRun');

        $name = $this . '_' . $suffix;

        if ($shotRun)
        {
            $this->returnCodeceptionActor()->putTempShot($name, $screenshot);

            usleep($defaultDelay);

            return $this;
        }

        $reference = $this->returnCodeceptionActor()->getShot($name);

        $timeout = (int) TestProperties::getValue('elementTimeout');

        $deadLine = microtime(True) + $timeout;

        while (microtime(True) < $deadLine)
        {
            /** @var Same|Diff $comparisonResult */
            $comparisonResult = $this->returnSelfieShooter()->compareImages($reference, $screenshot);

            if ($comparisonResult instanceof Same)
            {
                return $this;
            }

            usleep(500000);

            $screenshot = $this->returnSelfieShooter()->takeScreenshot('', $waitClosure);
        }

        $this->returnCodeceptionActor()->putTempShot($name, $screenshot);

        $viewportSize = $this->returnSeleniumElement()->get()->viewportSize();

        $diffImage = $this->returnSelfieShooter()->fitIntoDimensions($comparisonResult->diffImage, $viewportSize);

        $this->returnCodeceptionActor()->failSoft($this . ' -> не совпадает с сохранённым образцом: ' . $suffix, ['screenshot_blob' => $diffImage]);

        return $this;
    }

    /**
     * PageObject выглядит, как сохранённый эталон?
     *
     * Проверяет условие и возвращает true или false.
     *
     * В зависимости от значения параметра фреймворка "shotRun" данный метод ведёт себя по-разному.
     * Если shotRun == true, то метод просто сохраняет скриншот в каталог временных скриншотов (_data/shots/temp)
     * и затем возвращает $defaultValue.
     * Если shotRun == false, то метод сверяет текущий скриншот с эталоном.
     *
     * Параметр фреймворка "maxDeviation" задаёт максимальную допустимую погрешность для признания скриншотов одинаковыми.
     * Где 0 означает, что скриншоты совпадают полностью.
     *
     * Параметр "source" модуля ShotsStorageModule задаёт - откуда будет браться эталон.
     * Если source == local, то эталон будет браться из тестового каталога _data/shots.
     * Если source == remote, то эталон будет браться с S3, если его пока нет в _data/shots.
     *
     * @param string $suffix - если у PageObject'а есть несколько различных состояний, то скриншот для конкретного
     *                         состояния можно пометить с помощью суффикса. Желательно чтобы суффикс умещался в 8 символов.
     * @param bool $defaultValue
     * @param null $waitClosure - функция которую нужно вызвать после прокрутки элемента, например функция,
     *                            которая ждёт исчезновения ждуна
     * @return bool
     * @throws UsageException
     * @throws \ImagickException
     */
    public function isLikeBefore(string $suffix = 'default', bool $defaultValue = true, $waitClosure = null) : bool
    {
        WLogger::logInfo($this . ' -> выглядит, как сохранённый образец: ' . $suffix . '?');

        try
        {
            $this
                ->returnSeleniumElement()
                ->wait()
                ->until(Cond::pageLoaded())
            ;
        }
        catch (WaitUntilElement $e) {}

        $screenshot = $this->returnSelfieShooter()->takeScreenshot('', $waitClosure);

        $shotRun = (bool) TestProperties::getValue('shotRun');

        $name = $this . '_' . $suffix;

        if ($shotRun)
        {
            $this->returnCodeceptionActor()->putTempShot($name, $screenshot);

            return $defaultValue;
        }

        $reference = $this->returnCodeceptionActor()->getShot($name);

        return $this->returnSelfieShooter()->compareImages($reference, $screenshot) instanceof Same;
    }

    /**
     * @param AbstractOperation $visitor
     * @return mixed
     */
    public function accept($visitor)
    {
        return parent::accept($visitor);
    }

    public function getProxyWebElement() : ProxyWebElement
    {
        return $this->returnSeleniumElement()->returnProxyWebElement();
    }
}
