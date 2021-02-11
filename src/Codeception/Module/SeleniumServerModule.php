<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 07.02.19
 * Time: 15:15
 */

namespace Codeception\Module;


use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Selenium\Managers\ChromeDriverManager;
use Codeception\Lib\WFramework\Selenium\Managers\GeckoDriverManager;
use Codeception\Lib\WFramework\Selenium\Managers\SeleniumServerManager;
use Codeception\Module as CodeceptionModule;
use Codeception\Lib\WFramework\Exceptions\PortAlreadyInUseException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\UnixProcess;
use Codeception\Lib\WFramework\Logger\WLogger;
use Symfony\Component\Process\Process;
use function realpath;
use function stripos;

/**
 * Этот модуль служит для автозапуска Селениум Сервера перед прогоном тестов,
 * написанных с использованием WFramework.
 *
 * Так же он скачивает необходимые версии драйвера под Chrome и Firefox
 *
 * @package Common\Module\WFramework\Modules
 */
class SeleniumServerModule extends CodeceptionModule
{
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
     * @var false|resource
     */
    private $stream;

    /**
     * Запускает Селениум Сервер если он ещё не запущен.
     *
     * @throws PortAlreadyInUseException
     * @throws UsageException
     */
    public function startSeleniumServer()
    {
        WLogger::logNotice($this, 'Начинаем настройку и запуск Selenium Server.');

        $this->lock();

        if ($this->seleniumIsStarted())
        {
            WLogger::logDebug($this, 'Selenium Server уже запущен.');
            $this->unlock();
            return;
        }

        if (!$this->javaInstalled())
        {
            throw new UsageException('Не удалось найти Java. Установите java-runtime.');
        }

        $this->createOutputDir();

        $pathParams = $this->getPathParams();

        $cmd = "java $pathParams -port {$this->config['port']} -sessionTimeout {$this->config['sessionTimeout']}";

        WLogger::logDebug($this, 'Команда для запуска: ' . $cmd);

        $this->process = new UnixProcess($cmd); // Symfony/Process убивает Selenium Server при завершении тестов, а нам этого не нужно.
        // Selenium Server должен всегда быть в одном экземпляре.
        // Если другие тесты гоняются параллельно - пусть они используют уже запущенный
        // Selenium Server вместо того чтобы создавать свой экземпляр.
        $this->process->start();

        $seleniumIsStarted = $this->seleniumIsStarted(3);

        $this->unlock();

        if (!$seleniumIsStarted)
        {
            throw new PortAlreadyInUseException('Не удалось поднять Selenium Server на порту ' . $this->config['port'] . ' (возможно порт занят другим приложением или с Java что-то не так).');
        }
    }

    protected function seleniumIsStarted(int $maxTry = 1) : bool
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

    protected function lock()
    {
        $this->stream = false;
        $timeout = time() + 300;

        while (!$this->stream && time() < $timeout)
        {
            $this->stream = @stream_socket_server("tcp://127.0.0.1:{$this->config['lock']}", $errno, $errmg);

            if ($this->stream !== false)
            {
                return;
            }

            if ($this->seleniumIsStarted())
            {
                return;
            }

            WLogger::logDebug($this, 'Другой экземпляр скрипта пытается настроить и запустить Selenium Server - ждём');
            sleep(3);
        }

        if (!$this->stream)
        {
            throw new PortAlreadyInUseException("Другой процесс висит на порту {$this->config['lock']}. Нужно его убить.");
        }
    }

    protected function unlock()
    {
        if ($this->stream === false)
        {
            return;
        }

        fclose($this->stream);
    }

    protected function javaInstalled() : bool
    {
        $proc = new Process(['which', 'java']);
        $proc->run();
        $output = $proc->getOutput();

        return !empty($output);
    }

    protected function createOutputDir()
    {
        $homeDir = $this->getHomeDir();

        if ($homeDir === null)
        {
            throw new GeneralException('Не удалось получить домашнюю директорию');
        }

        $outputDir = $this->getHomeDir() . '/' . $this->config['outputFolder'];

        if (!is_dir($outputDir))
        {
            mkdir($outputDir, 0777, true);
        }

        $dir = realpath($outputDir);

        if ($dir === false)
        {
            throw new GeneralException("Не получилось создать директорию: $outputDir");
        }

        $this->config['outputDir'] = $dir;
    }

    protected function getHomeDir() :?string
    {
        // getenv('HOME') isn't set on Windows and generates a Notice.
        $home = getenv('HOME');

        if (!empty($home))
        {
            // home should never end with a trailing slash.
            return rtrim($home, '/');
        }

        if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH']))
        {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            $home = rtrim($home, '\\/');
        }

        return empty($home) ? null : $home;
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
