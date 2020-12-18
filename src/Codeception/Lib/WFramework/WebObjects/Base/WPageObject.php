<?php


namespace Codeception\Lib\WFramework\WebObjects\Base;


use Codeception\Actor;
use Codeception\Lib\WFramework\Actor\ImaginaryActor;
use Codeception\Lib\WFramework\Conditions\PageLoaded;
use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Helpers\Composite;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteActions;
use Codeception\Lib\WFramework\Operations\Get\GetRawText;
use Codeception\Lib\WFramework\Operations\Get\GetText;
use Codeception\Lib\WFramework\Operations\Get\GetLayoutViewportSize;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebDriver;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElementActions;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\Traits\PageObjectBaseMethods;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\ComparisonResult\Diff;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\ComparisonResult\Same;
use Codeception\Lib\WFramework\WebObjects\SelfieShooter\SelfieShooter;
use Codeception\Lib\WFramework\WLocator\EmptyLocator;
use Codeception\Lib\WFramework\WLocator\WLocator;
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
    use PageObjectBaseMethods;

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
     * Сервер Селениума обёрнут в прокси-объект, чтобы
     *
     * @var ProxyWebDriver
     */
    protected $proxyWebDriver = null;

    /**
     * Все PageObject'ы имеют ссылку на свой элемент Селениума, чтобы дёргать его методы
     *
     * @var  ProxyWebElement
     */
    protected $proxyWebElement = null;

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
     * @return ProxyWebElement
     * @throws UsageException
     */
    public function returnSeleniumElement() : ProxyWebElement
    {
        if ($this->proxyWebElement === null)
        {
            if ($this->relative === true)
            {
                $this->proxyWebElement = new ProxyWebElement($this->locator, $this->returnSeleniumServer(), $this->getTimeout(), $this->getParent()->returnSeleniumElement());
            }
            else
            {
                $this->proxyWebElement = new ProxyWebElement($this->locator, $this->returnSeleniumServer(), $this->getTimeout());
            }
        }

        return $this->proxyWebElement;
    }

    /**
     * С помощью этого метода можно обратиться к методам Сервера Селениума
     *
     * @return RemoteWebDriver
     * @throws UsageException
     */
    public function returnSeleniumServer() : RemoteWebDriver
    {
        if ($this->proxyWebDriver === null)
        {
            if ($this->getParent() instanceof EmptyComposite)
            {
                throw new UsageException($this . ' -> не содержит ссылку на webDriver. Это странно.');
            }

            $this->proxyWebDriver = $this->getParent()->returnSeleniumServer();
        }

        return $this->proxyWebDriver;
    }

    /**
     * С помощью этого метода можно обратиться к методам Selenium Actions
     *
     * https://selenium.dev/selenium/docs/api/java/org/openqa/selenium/interactions/Actions.html
     *
     * @return WebDriverActions
     * @throws UsageException
     */
    public function returnSeleniumActions() : ProxyWebElementActions
    {
        return $this->accept(new ExecuteActions());
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
                throw new UsageException($this . ' -> не содержит ссылку на актора. Это странно.');
            }

            $this->actor = $this->getParent()->returnCodeceptionActor();
        }

        return $this->actor;
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

        $result = $this->accept(new GetText());

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

        $result = $this->accept(new GetRawText());

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

        $this->accept(new MouseScrollTo());

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

        $this->finally_(new PageLoaded());

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

        $viewportSize = $this->accept(new GetLayoutViewportSize());

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

        $this->finally_(new PageLoaded());

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
     * @param PageObjectVisitor $visitor
     * @return mixed
     */
    public function accept($visitor)
    {
        return parent::accept($visitor);
    }

    public function getTimeout() : int
    {
        return (int) TestProperties::getValue('elementTimeout');
    }

    /**
     * @return EmptyComposite|WPageObject
     * @throws UsageException
     */
    public function getParent()
    {
        $parent = parent::getParent();

        if (!$parent instanceof EmptyComposite && !$parent instanceof WPageObject)
        {
            throw new UsageException($this . ' -> родителем WPageObject должен быть другой WPageObject или EmptyComposite');
        }

        return $parent;
    }
}
