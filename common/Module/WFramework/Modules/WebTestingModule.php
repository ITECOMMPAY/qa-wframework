<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.02.19
 * Time: 16:26
 */

namespace Common\Module\WFramework\Modules;


use BrowserStack\Local;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module\WebDriver;
use Codeception\TestInterface;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\GlobalProperties;
use Common\Module\WFramework\Properties\SuiteProperties;
use Common\Module\WFramework\ProxyWebDriver\ProxyWebDriver;
use Common\Module\WFramework\Properties\TestProperties;
use function getenv;
use function getmypid;
use function md5;
use function microtime;
use function php_uname;
use function phpversion;
use function posix_getlogin;
use function strtoupper;

/**
 * Основной модуль WFramework
 *
 * @package Common\Module\WFramework\Modules
 */
class WebTestingModule extends WebDriver implements DependsOnModule
{
    /**  @var LoggerModule */
    protected $log;

    /**  @var SeleniumServerModule */
    protected $seleniumServer;

    /** @var ProxyWebDriver */
    protected $proxyWebDriver;

    /** @var Local */
    protected $bs_local;

    /** @var bool */
    protected $firstInit = true;

    protected $config = [
        'protocol'                => 'http',
        'host'                    => '127.0.0.1',
        'port'                    => '4444',
        'path'                    => '/wd/hub',
        'start'                   => true,
        'restart'                 => false,
        'wait'                    => 0,
        'clear_cookies'           => true,
        'window_size'             => false,
        'capabilities'            => [],
        'connection_timeout'      => null,
        'request_timeout'         => null,
        'pageload_timeout'        => null,
        'http_proxy'              => null,
        'http_proxy_port'         => null,
        'ssl_proxy'               => null,
        'ssl_proxy_port'          => null,
        'debug_log_entries'       => 15,
        'log_js_errors'           => false,
        'elementTimeout'          => 6,
        'collectionTimeout'       => 12,
        'clickViaJS'              => false,
        'autoClickViaJS'          => false,
        'clickOnHref'             => true,
        'topBarHeight'            => 0,
        'holdBrowserOpen'         => false,
        'autostartSeleniumServer' => true,
        'restartBeforeEachTest'   => true,
        'autoUpdateDrivers'       => true,
        'debug'                   => true,
        'takeScreenshots'         => true,
        'screenshotsToVideo'      => true,
        'shotRun'                 => true,
        'maxDeviation'            => 10,
        'useBrowserStack'         => false
    ];

    protected $requiredFields = ['browser'];

    protected $dependencyMessage = <<<EOF
Example configuring PhpBrowser as backend for WebModule module.
--
modules:
    enabled:
        - Common\Module\WFramework\Modules\LoggerModule
        - Common\Module\WFramework\Modules\SeleniumServerModule
        - Common\Module\WFramework\Modules\WebAssertsModule
        - Common\Module\WFramework\Modules\ShotsStorageModule
        - Common\Module\WFramework\Modules\WebTestingModule:
            depends:
                - Common\Module\WFramework\Modules\LoggerModule
                - Common\Module\WFramework\Modules\SeleniumServerModule
                - Common\Module\WFramework\Modules\WebAssertsModule
                - Common\Module\WFramework\Modules\ShotsStorageModule
            browser: 'chrome'
            autostartSeleniumServer: true
            clickViaJS: false
            autoClickViaJS: true
            topBarHeight: 65
            window_size: '1366x768'
            holdBrowserOpen: true
--
EOF;

    public function _inject(LoggerModule $log, SeleniumServerModule $seleniumServer)
    {
        $this->proxyWebDriver = new ProxyWebDriver();

        $this->log = $log
                        ->_setDebug($this->config['debug'])
                        ->_setWebDriver($this->proxyWebDriver)
                        ->_setTakeScreenshots($this->config['takeScreenshots'])
                        ->_setScreenshotsToVideo($this->config['screenshotsToVideo'])
                        ;

        $this->seleniumServer = $seleniumServer;
    }

    public function _initialize()
    {
        WLogger::logInfo('Initializing framework');

        if ($this->firstInit)
        {
            if ($this->config['useBrowserStack'])
            {
                $this->configureBrowserStack();
            }
            elseif ($this->config['autostartSeleniumServer'])
            {
                $this->seleniumServer->startSeleniumServer($this->config['autoUpdateDrivers']);
            }

            $this->setRunUniqueId();

            $this->backupConfig = $this->config;

            GlobalProperties::setValues($this->config);

            $this->firstInit = false;
        }

        parent::_initialize();
    }

    protected function configureBrowserStack()
    {
        WLogger::logDebug('Configuring Browser Stack');

        getenv('BROWSERSTACK_USERNAME')   ? ($this->config['capabilities']['browserstack.user'] = getenv('BROWSERSTACK_USERNAME')) : 0;
        getenv('BROWSERSTACK_ACCESS_KEY') ? ($this->config['capabilities']['browserstack.key'] = getenv('BROWSERSTACK_ACCESS_KEY')) : 0;
        getenv('BROWSERSTACK_BUILD') ? ($this->config['capabilities']['build'] = getenv('BROWSERSTACK_BUILD')) : 0;
        getenv('BROWSERSTACK_PROJECT') ? ($this->config['capabilities']['project'] = getenv('BROWSERSTACK_PROJECT')) : 0;

        $this->config['capabilities']['browserstack.localIdentifier'] = md5(posix_getlogin() . php_uname() . phpversion() . random_bytes(64));

        $browserStackLocal = $this->config['capabilities']['browserstack.local'] ?? false;

        if ($browserStackLocal)
        {
            $this->startLocalBrowserStack();
        }
    }

    protected function startLocalBrowserStack()
    {
        WLogger::logDebug('Running local Browser Stack');

        if (!isset($this->bs_local))
        {
            $bs_local_args = [
                'key' => $this->config['capabilities']['browserstack.key'],
                'localIdentifier' => $this->config['capabilities']['browserstack.localIdentifier'],
                'forcelocal' => true
                ];

            $this->bs_local = new Local();

            $attempt = 0;

            while(!$this->bs_local->isRunning() && $attempt < 3)
            {
                try
                {
                    $this->bs_local->start($bs_local_args);
                    break;
                }
                catch (\BrowserStack\LocalException $e)
                {
                    $attempt++;
                    sleep(random_int(3, 5));
                }
            }
        }
    }

    protected function setRunUniqueId()
    {
        exec('hostname -I', $hostname);

        $id = hash('crc32', implode('', $hostname) . getmypid() . microtime());

        $this->config['runUid'] = strtoupper($id);
    }

    /**
     * Specifies class or module which is required for current one.
     *
     * THis method should return array with key as class name and value as error message
     * [className => errorMessage
     * ]
     * @return mixed
     */
    public function _depends()
    {
        return [LoggerModule::class => $this->dependencyMessage,
                SeleniumServerModule::class => $this->dependencyMessage,
                WebAssertsModule::class => $this->dependencyMessage,
                ShotsStorageModule::class => $this->dependencyMessage];
    }

    /**
     * WBlock дёргает этот метод в своём конструкторе у актора, чтобы получить ссылку на RemoteWebDriver для своей работы.
     * WElement получает ссылку на RemoteWebDriver от WBlock на котором расположен.
     *
     * RemoteWebDriver обёрнут в прокси, чтобы при перезапуске WebDriver можно было легко передать ссылку на новый
     * экземпляр RemoteWebDriver во все PageObject'ы которые его используют.
     *
     * @return ProxyWebDriver
     */
    public function getWebDriver() : ProxyWebDriver
    {
        return $this->proxyWebDriver;
    }

    public function _initializeSession()
    {
        parent::_initializeSession();

        // Старый экземпляр RemoteWebDriver - мёртв, и ссылка на него во всех PageObject'ах - невалидна.
        // Но т.к. эта ссылка была обёрнута в ProxyWebDriver, то достаточно передать в него свежий
        // экземпляр RemoteWebDriver, чтобы актуализировать ссылки всех PageObject'ов.

        if (!isset($this->proxyWebDriver))
        {
            $this->proxyWebDriver = new ProxyWebDriver();
        }

        $this->proxyWebDriver->setRemoteWebDriver($this->webDriver);
    }

    public function _closeSession($webDriver = null)
    {
        if ($this->config['holdBrowserOpen'] === true)
        {
            return;
        }

        parent::_closeSession($webDriver);
    }

    public function restartWebDriver()
    {
        $this->_restart();
    }

    public function _before(TestInterface $test)
    {
        if ($this->config['useBrowserStack'])
        {
            $name = $test->getMetadata()->getName();

            $this->_capabilities(
                function ($currentCapabilities) use ($name)
                {
                    $currentCapabilities['name'] = $name;
                    return $currentCapabilities;
                }
            );
        }

        if (isset($this->webDriver) && $this->config['restartBeforeEachTest'] === True)
        {
            $this->restartWebDriver();
        }

        TestProperties::clear();

        parent::_before($test);
    }

    public function _beforeSuite($settings = [])
    {
        SuiteProperties::clear();

        parent::_beforeSuite($settings);
    }

    /**
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        parent::_failed($test, $fail);

        WLogger::logError($fail->getMessage());
    }

    public function __destruct()
    {
        if (isset($this->bs_local) && $this->bs_local->isRunning())
        {
            $this->bs_local->stop();
        }
    }
}
