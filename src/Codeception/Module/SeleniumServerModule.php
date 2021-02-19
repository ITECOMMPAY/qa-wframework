<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 07.02.19
 * Time: 15:15
 */

namespace Codeception\Module;


use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Lib\ModuleContainer;
use Codeception\Lib\WFramework\Helpers\MultiProcessLock;
use Codeception\Lib\WFramework\Helpers\System;
use Codeception\Lib\WFramework\Selenium\Managers\ChromeDriverManager;
use Codeception\Lib\WFramework\Selenium\Managers\GeckoDriverManager;
use Codeception\Lib\WFramework\Selenium\Managers\SeleniumServerManager;
use Codeception\Module as CodeceptionModule;
use Codeception\Lib\WFramework\Exceptions\PortAlreadyInUseException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\UnixProcess;
use Codeception\Lib\WFramework\Logger\WLogger;
use Symfony\Component\Process\Process;
use function stripos;

/**
 * Этот модуль служит для автозапуска Селениум Сервера перед прогоном тестов,
 * написанных с использованием WFramework.
 *
 * Так же он скачивает необходимые версии драйвера под Chrome и Firefox
 *
 * @package Common\Module\WFramework\Modules
 */
class SeleniumServerModule extends CodeceptionModule implements DependsOnModule
{
    /** @var WebTestingModule */
    protected $webTestingModule;

    /** @var UnixProcess */
    protected $process;

    protected $config = [
        'autoUpdateDrivers'           => true,
        'outputFolder'                => '.wselenium',
        'chromeDriverUrl'             => "https://chromedriver.storage.googleapis.com",
        'geckoDriverUrl'              => "https://api.github.com/repos/mozilla/geckodriver/releases",
        'seleniumServerStandaloneUrl' => "https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar",
        'sessionTimeout'              => 3600,
        'port'                        => 4444,
        'lock'                        => 25439
    ];

    /**
     * @var MultiProcessLock
     */
    private $multiProcessLock;

    protected $dependencyMessage = <<<EOF
Configuration example.
--
modules:
    enabled:
        - Codeception\Module\SeleniumServerModule
        - Codeception\Module\FFmpegManagerModule
        - Codeception\Module\WebAssertsModule
        - Codeception\Module\ShotsStorageModule:
EOF;

    public function _depends()
    {
        return [
            WebTestingModule::class => $this->dependencyMessage,
        ];
    }

    public function _inject(WebTestingModule $webTestingModule)
    {
        $this->webTestingModule = $webTestingModule;
    }

    protected function getWebTestingModule() : WebTestingModule
    {
        return $this->webTestingModule;
    }

    public function __construct(
        ModuleContainer $moduleContainer,
        $config = null
    )
    {
        parent::__construct($moduleContainer, $config);
        $this->multiProcessLock = new MultiProcessLock($this->config['lock']);
    }

    public function _initialize()
    {
        if ($this->getWebTestingModule()->_doINeedAutostartSeleniumServer())
        {
            $this->startSeleniumServer();
        }
    }

    /**
     * Запускает Селениум Сервер если он ещё не запущен.
     *
     * @throws PortAlreadyInUseException
     * @throws UsageException
     */
    public function startSeleniumServer()
    {
        WLogger::logNotice($this, 'Начинаем настройку и запуск Selenium Server');

        $this->multiProcessLock->lock([$this, 'seleniumIsStarted']);

        if ($this->seleniumIsStarted())
        {
            WLogger::logDebug($this, 'Selenium Server уже запущен');
            $this->multiProcessLock->unlock();
            return;
        }

        if (!$this->javaInstalled())
        {
            throw new UsageException('Не удалось найти Java. Установите java-runtime');
        }

        $this->config['outputDir'] = System::mkDirInHome($this->config['outputFolder']);

        $pathParams = $this->getPathParams();

        $cmd = "java $pathParams -port {$this->config['port']} -sessionTimeout {$this->config['sessionTimeout']}";

        WLogger::logDebug($this, 'Команда для запуска: ' . $cmd);

        $this->process = new UnixProcess($cmd); // Symfony/Process убивает Selenium Server при завершении тестов, а нам этого не нужно.
        // Selenium Server должен всегда быть в одном экземпляре.
        // Если другие тесты гоняются параллельно - пусть они используют уже запущенный
        // Selenium Server вместо того чтобы создавать свой экземпляр.
        $this->process->start();

        $seleniumIsStarted = $this->seleniumIsStarted(3);

        $this->multiProcessLock->unlock();

        if (!$seleniumIsStarted)
        {
            throw new PortAlreadyInUseException('Не удалось поднять Selenium Server на порту ' . $this->config['port'] . ' (возможно порт занят другим приложением или с Java что-то не так)');
        }
    }

    public function seleniumIsStarted(int $maxTry = 1) : bool
    {
        $try = 0;

        while (!$this->seleniumPortIsOpen() && $try < $maxTry)
        {
            $try++;

            if ($try === $maxTry)
            {
                return false;
            }

            sleep(1);
        }

        while (stripos(@file_get_contents("http://localhost:{$this->config['port']}/wd/hub/status"), 'Server is running') === false)
        {
            $try++;

            if ($try === $maxTry)
            {
                return false;
            }

            sleep(1);
        }

        return true;
    }

    protected function seleniumPortIsOpen() : bool
    {
        $socket = @fsockopen('localhost', $this->config['port'], $errno, $errstr, 2);

        if (is_resource($socket))
        {
            fclose($socket);
            return true;
        }

        return false;
    }

    protected function javaInstalled() : bool
    {
        $proc = new Process(['which', 'java']);
        $proc->run();
        $output = $proc->getOutput();

        return !empty($output);
    }

    protected function getPathParams() : string
    {
        $result = [];

        $chromeDriverParam = (new ChromeDriverManager($this->config))->get();

        if (!empty($chromeDriverParam))
        {
            $result[] = $chromeDriverParam;
        }

        $firefoxDriverParam = (new GeckoDriverManager($this->config))->get();

        if (!empty($firefoxDriverParam))
        {
            $result[] = $firefoxDriverParam;
        }

        $seleniumJarParam = (new SeleniumServerManager($this->config))->get();

        if (!empty($seleniumJarParam))
        {
            $result[] = $seleniumJarParam;
        }

        return implode(' ', $result);
    }
}
