<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 07.02.19
 * Time: 15:15
 */

namespace Codeception\Module;

use Codeception\Module as CodeceptionModule;
use Codeception\Lib\WFramework\Exceptions\SeleniumServerModule\PortAlreadyInUseException;
use Codeception\Lib\WFramework\Exceptions\SeleniumServerModule\UsageException;
use Codeception\Lib\WFramework\Helpers\CurrentOS;
use Codeception\Lib\WFramework\Helpers\UnixProcess;
use Codeception\Lib\WFramework\Logger\WLogger;
use PharData;
use Symfony\Component\Process\Process;
use function chmod;
use function codecept_output_dir;
use function copy;
use function file_exists;
use function file_put_contents;
use function fopen;
use function json_decode;
use function preg_match;
use function realpath;
use function stripos;
use function strlen;
use function substr;
use function unlink;

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

    const SELENIUM_BIN_PATH = '/../Lib/WFramework/SeleniumBin';
    const SELENIUM_SERVER_PATH = self::SELENIUM_BIN_PATH . '/selenium-server-standalone.jar';
    const SELENIUM_STATUS_URL = 'http://localhost:4444/wd/hub/status';

    /**
     * @var false|resource
     */
    private $stream;

    /**
     * Запускает Селениум Сервер если он ещё не запущен.
     *
     * @param bool $autoUpdateDrivers - если в true, то перед запуском Селениум Сервера будут скачаны необходимые версии
     *                                  драйвера
     * @throws PortAlreadyInUseException
     * @throws UsageException
     */
    public function startSeleniumServer(bool $autoUpdateDrivers = false)
    {
        WLogger::logDebug('Начинаем настройку и запуск Selenium Server.');

        $this->lock();

        if ($this->seleniumIsStarted())
        {
            WLogger::logDebug('Selenium Server уже запущен.');
            $this->unlock();
            return;
        }

        $paths = $this->getDefaultDriverPaths();

        if ($autoUpdateDrivers === true)
        {
            $this->updateDrivers($paths);
        }

        $chromedriverPath = $paths['chromedriver'];
        $geckodriverPath = $paths['geckodriver'];
        $seleniumServerPath = realpath(__DIR__ . static::SELENIUM_SERVER_PATH);

        $cmd = "java -Dwebdriver.chrome.driver=$chromedriverPath -Dwebdriver.gecko.driver=$geckodriverPath -jar $seleniumServerPath -sessionTimeout 7200";

        WLogger::logDebug('Команда для запуска: ' . $cmd);

        $this->process = new UnixProcess($cmd); // Symfony/Process убивает Selenium Server при завершении тестов, а нам этого не нужно.
        // Selenium Server должен всегда быть в одном экземпляре.
        // Если другие тесты гоняются параллельно - пусть они используют уже запущенный
        // Selenium Server вместо того чтобы создавать свой экземпляр.
        $this->process->start();

        $seleniumIsStarted = $this->seleniumIsStarted(3);

        $this->unlock();

        if (!$seleniumIsStarted)
        {
            throw new PortAlreadyInUseException('Не удалось поднять Selenium Server на порте 4444 (возможно порт занят другим приложением или вообще Java не стоит).');
        }
    }

    protected function getDefaultDriverPaths() : array
    {
        $os = CurrentOS::get();

        $chromepath = static::SELENIUM_BIN_PATH;
        $geckopath = static::SELENIUM_BIN_PATH;

        switch ($os)
        {
            case CurrentOS::LINUX:
                $chromepath .= '/chromedriver_linux64_default';
                $geckopath  .= '/geckodriver_linux64_default';
                break;

            case CurrentOS::MAC:
                $chromepath .= '/chromedriver_mac64_default';
                $geckopath  .= '/geckodriver_mac64_default';
                break;

            default:
                throw new UsageException('Данный модуль работает только под GNU/Linux и Mac. Текущая ОС: ' . $os);
        }

        $chromepath = realpath(__DIR__ . $chromepath);
        $geckopath = realpath(__DIR__ . $geckopath);

        $paths = [
            'chromedriver' => $chromepath,
            'geckodriver'  => $geckopath
        ];

        return $paths;
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

        $status = @file_get_contents(static::SELENIUM_STATUS_URL);

        return stripos($status, 'Server is running') !== false;
    }

    protected function seleniumPortIsOpen() : bool
    {
        $socket = @fsockopen('localhost', 4444, $errno, $errstr, 2);

        if (is_resource($socket))
        {
            fclose($socket);
            return true;
        }

        return false;
    }

    public function stopSeleniumServer()
    {
        if (!$this->process->isRunning())
        {
            return;
        }

        WLogger::logDebug('Останавливаем Selenium Server.');

        $this->process->stop();
    }

    protected function lock()
    {
        $this->stream = false;
        $timeout = time() + 300;

        while (!$this->stream && time() < $timeout)
        {
            $this->stream = @stream_socket_server('tcp://127.0.0.1:25439', $errno, $errmg);

            if ($this->stream !== false)
            {
                return;
            }

            if ($this->seleniumIsStarted())
            {
                return;
            }

            WLogger::logDebug('Другой экземпляр скрипта пытается настроить и запустить Selenium Server - ждём');
            sleep(3);
        }

        if (!$this->stream)
        {
            throw new PortAlreadyInUseException('Другой процесс висит на порту 25439. Нужно его убить.');
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

    protected function updateDrivers(array &$paths)
    {
        $chromedriverPath = $this->updateChromeDriver($paths['chromedriver']);
        $paths['chromedriver'] = $chromedriverPath;

        $geckodriverPath = $this->updateFirefoxDriver($paths['geckodriver']);
        $paths['geckodriver'] = $geckodriverPath;
    }

    protected function updateChromeDriver(string $defaultLocalChromedriver) : string
    {
        WLogger::logDebug('Получаем версию Google Chrome');

        if ($this->chromiumIsFromSnap())
        {
            throw new UsageException('Chromium установлен из Snap. Selenium пока не умеет с ним работать: 
            https://github.com/SeleniumHQ/selenium/issues/7788 Удалите пакет с компьютера: sudo snap remove chromium - 
            и поставьте Хром с сайта Гугла.');
        }

        $chromeVersion = $this->getBrowserVersion(['google-chrome', 'chromium-browser', 'chrome', 'chromium'], '--version', '%\s+(\d+).\d+.\d+.\d+%m');

        if (empty($chromeVersion))
        {
            WLogger::logDebug('Не удалось установить версию Google Chrome - используем дефолтный chromedriver');
            return $defaultLocalChromedriver;
        }

        WLogger::logDebug("Версия Google Chrome: $chromeVersion");

        $currentLocalChromedriver = substr($defaultLocalChromedriver, 0, strlen($defaultLocalChromedriver) - strlen('default')) . $chromeVersion;

        if (file_exists($currentLocalChromedriver))
        {
            WLogger::logDebug('chromedriver уже скачан - используем его');
            return $currentLocalChromedriver;
        }

        WLogger::logDebug("Скачиваем chromedriver под Google Chrome $chromeVersion");

        $chromedriverVersion = @file_get_contents("https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$chromeVersion");

        if ($chromedriverVersion === false)
        {
            WLogger::logDebug("Не удалось установить версию chromedriver для Google Chrome $chromeVersion - используем дефолтный");
            return $defaultLocalChromedriver;
        }

        if (stripos($currentLocalChromedriver, 'linux64') !== false)
        {
            $url = "https://chromedriver.storage.googleapis.com/$chromedriverVersion/chromedriver_linux64.zip";
        }

        if (stripos($currentLocalChromedriver, 'mac64') !== false)
        {
            $url = "https://chromedriver.storage.googleapis.com/$chromedriverVersion/chromedriver_mac64.zip";
        }

        $zipfile = codecept_output_dir() . '/chromedriver_temp.zip';

        if (!@file_put_contents($zipfile, fopen($url, 'rb')))
        {
            WLogger::logDebug("Не удалось скачать $url - используем дефолтный chromedriver");
            return $defaultLocalChromedriver;
        }

        if (!copy('zip://' . $zipfile . '#chromedriver', $currentLocalChromedriver))
        {
            WLogger::logDebug('Не удалось распаковать скачанный chromedriver - используем дефолтный');
            return $defaultLocalChromedriver;
        }

        unlink($zipfile);

        chmod($currentLocalChromedriver, 0775);

        return $currentLocalChromedriver;
    }

    protected function chromiumIsFromSnap() : bool
    {
        $proc = new Process(['which', 'chromium']);
        $proc->run();
        $output = $proc->getOutput();

        if (empty($output) || stripos($output, 'snap') === false)
        {
            return false;
        }

        if (!file_exists('/snap/bin/chromium.chromedriver'))
        {
            return false;
        }

        return true;
    }

    protected function updateFirefoxDriver(string $defaultLocalGeckodriver) : string
    {
        WLogger::logDebug('Получаем версию Firefox');

        $firefoxVersion = $this->getBrowserVersion(['firefox', 'iceweasel'], '--version', '%\s+(\d+).\d+%m');

        if (empty($firefoxVersion))
        {
            WLogger::logDebug('Не удалось установить версию Firefox - используем дефолтный geckodriver');
            return $defaultLocalGeckodriver;
        }

        WLogger::logDebug("Версия Firefox: $firefoxVersion");

        $currentLocalGeckodriver = substr($defaultLocalGeckodriver, 0, strlen($defaultLocalGeckodriver) - strlen('default')) . $firefoxVersion;

        if (file_exists($currentLocalGeckodriver))
        {
            WLogger::logDebug('geckodriver уже скачан - используем его');
            return $currentLocalGeckodriver;
        }

        WLogger::logDebug('Скачиваем последний geckodriver');

        $seleniumBinDir = pathinfo($defaultLocalGeckodriver, PATHINFO_DIRNAME);

        $arrContextOptions = [
            'http' => [
                'user_agent' => 'Some/1.0 downloader'
            ]
        ];

        $latestReleaseInfo = @file_get_contents('https://api.github.com/repos/mozilla/geckodriver/releases/latest', false, stream_context_create($arrContextOptions));

        if ($latestReleaseInfo === false)
        {
            WLogger::logDebug('Не удалось установить версию последнего geckodriver с github - используем дефолтный');
            return $defaultLocalGeckodriver;
        }

        $info = json_decode($latestReleaseInfo, true);

        if (!isset($info['assets'][0]['browser_download_url']))
        {
            WLogger::logDebug('Формат ответа от github изменился - используем дефолтный geckodriver');
            return $defaultLocalGeckodriver;
        }

        if (stripos($currentLocalGeckodriver, 'linux64') !== false)
        {
            $needle = 'linux64';
        }

        if (stripos($currentLocalGeckodriver, 'mac64') !== false)
        {
            $needle = 'macos';
        }

        $url = '';

        foreach ($info['assets'] as $asset)
        {
            $browserDownloadUrl = $asset['browser_download_url'];

            if (stripos($browserDownloadUrl, $needle) !== false)
            {
                $url = $browserDownloadUrl;
                break;
            }
        }

        if (empty($url))
        {
            WLogger::logDebug("Не удалось найти последний geckodriver для $needle на github - используем дефолтный");
            return $defaultLocalGeckodriver;
        }

        $tarFile = codecept_output_dir() . '/geckodriver_temp.tar';
        @unlink($tarFile);

        $gzFile = $tarFile . '.gz';

        if (!@file_put_contents($gzFile, fopen($url, 'rb')))
        {
            WLogger::logDebug("Не удалось скачать $url - используем дефолтный geckodriver");
            return $defaultLocalGeckodriver;
        }

        $gz = new PharData($gzFile);
        $gz->decompress();

        $tar = new PharData($tarFile);
        if (!$tar->extractTo($seleniumBinDir, 'geckodriver', true))
        {
            WLogger::logDebug('Не удалось распаковать скачанный geckodriver - используем дефолтный');
            return $defaultLocalGeckodriver;
        }

        $temp = realpath($seleniumBinDir . '/geckodriver');

        if (!$temp)
        {
            WLogger::logDebug('Не удалось распаковать скачанный geckodriver - используем дефолтный');
            return $defaultLocalGeckodriver;
        }

        rename($temp, $currentLocalGeckodriver);

        unlink($gzFile);
        unlink($tarFile);

        chmod($currentLocalGeckodriver, 0775);

        return $currentLocalGeckodriver;
    }

    protected function getBrowserVersion(array $binaryNames, string $versionFlag, string $regex) : string
    {
        foreach ($binaryNames as $binaryName)
        {
            $proc = new Process([$binaryName, $versionFlag]);
            $proc->run();
            $output = $proc->getOutput();

            if (empty($output))
            {
                continue;
            }

            preg_match($regex, $output, $matches);

            if (isset($matches[1]))
            {
                return $matches[1];
            }
        }

        return '';
    }
}
