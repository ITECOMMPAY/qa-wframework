<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.02.19
 * Time: 16:26
 */

namespace Codeception\Module;


use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\TestInterface;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\GlobalProperties;
use Codeception\Lib\WFramework\Properties\SuiteProperties;
use Codeception\Lib\WFramework\ProxyWebDriver\ProxyWebDriver;
use Codeception\Lib\WFramework\Properties\TestProperties;
use function getenv;
use function getmypid;
use function microtime;
use function php_uname;
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
        'clear_cookies'           => false,
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

    protected $requiredFields = [];

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
        if ($this->firstInit)
        {
            WLogger::logInfo('Initializing framework');

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
        WLogger::logDebug('Настраиваем Browser Stack');

        getenv('BROWSERSTACK_USERNAME')   ? ($this->config['capabilities']['browserstack.user'] = getenv('BROWSERSTACK_USERNAME')) : 0;
        getenv('BROWSERSTACK_ACCESS_KEY') ? ($this->config['capabilities']['browserstack.key'] = getenv('BROWSERSTACK_ACCESS_KEY')) : 0;
        getenv('BROWSERSTACK_BUILD') ? ($this->config['capabilities']['build'] = getenv('BROWSERSTACK_BUILD')) : 0;
        getenv('BROWSERSTACK_PROJECT') ? ($this->config['capabilities']['project'] = getenv('BROWSERSTACK_PROJECT')) : 0;

        $local = $this->config['capabilities']['browserstack.local'] ?? false;

        if ($local)
        {
            $this->config['capabilities']['browserstack.localIdentifier'] = $this->getBSLocalId();
        }

        /*
         * Локальный BrowserStack нужно запустить руками, через их Jenkins плагин или через /common/Module/WFramework/Helpers/BSStarter.php
         *
         * BSStarter.php гарантирует, что даже если несколько тестов одновременно попробуют запустить локального агента BS
         * - только один экземпляр скрипта сделает это, в то время как остальные будут ждать успешного запуска агента.
         */
    }

    protected function getBSLocalId() : string
    {
        return preg_replace("/[^A-Za-z0-9]/", '', php_uname());
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

        if ($this->config['useBrowserStack'] && isset($this->webDriver))
        {
            try
            {
                $script = 'browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"failed","reason":"fail"}}';

                $this->webDriver->executeScript($script);
            }
            catch (\Throwable $e)
            {

            }
        }
    }
}
