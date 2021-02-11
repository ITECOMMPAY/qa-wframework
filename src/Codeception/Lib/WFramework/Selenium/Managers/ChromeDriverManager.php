<?php


namespace Codeception\Lib\WFramework\Selenium\Managers;


use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\NotImplementedException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\CurrentOS;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Selenium\AbstractDriverManager;
use Symfony\Component\Process\Process;

class ChromeDriverManager extends AbstractDriverManager
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $outputDir;

    /** @var bool */
    protected $autoUpdate;

    public function __construct(array $config)
    {
        $this->url = $config['chromeDriverUrl'];
        $this->outputDir = $config['outputDir'];
        $this->autoUpdate = $config['autoUpdateDrivers'];
    }

    public function get() : string
    {
        $os = $this->getOS();

        $chromeVersion = $this->getChromeVersion();

        if (empty($chromeVersion))
        {
            WLogger::logDebug($this, 'Не удалось получить версию Google Chrome - считаем, что он не установлен');
            return '';
        }

        $driverLocalName = $this->outputDir . "/chromedriver_{$os}_{$chromeVersion}";

        if (file_exists($driverLocalName))
        {
            WLogger::logDebug($this, 'chromedriver уже скачан - используем его');
            return $this->asParam($driverLocalName);
        }

        if (!$this->autoUpdate)
        {
            throw new UsageException("autoUpdateDrivers = false, но драйвер $driverLocalName - отсутствует");
        }

        $this->downloadChrome($os, $chromeVersion, $driverLocalName);

        return $this->asParam($driverLocalName);
    }

    protected function getChromeVersion() : string
    {
        WLogger::logDebug($this, 'Получаем версию Google Chrome');

        ['name' => $chromeName, 'version' => $chromeVersion] = $this->getBrowserInfo(['google-chrome', 'chromium-browser', 'chrome', 'chromium'], '--version', '%\s+(\d+).\d+.\d+.\d+%m');

        if (empty($chromeVersion))
        {
            return '';
        }

        if (in_array($chromeName, ['chromium', 'chromium-browser'], true) && $this->chromiumFromSnap())
        {
            throw new NotImplementedException('Chromium установлен из Snap. Selenium пока не умеет с ним работать: 
            https://github.com/SeleniumHQ/selenium/issues/7788 Удалите пакет с компьютера: sudo snap remove chromium - 
            и поставьте Хром с сайта Гугла.');
        }

        WLogger::logDebug($this, "Версия Google Chrome: $chromeVersion");

        return $chromeVersion;
    }

    protected function chromiumFromSnap() : bool
    {
        $proc = new Process(['which', 'chromium']);
        $proc->run();
        $output = $proc->getOutput();

        return !(stripos($output, 'snap') === false);
    }

    protected function asParam(string $driverPath) : string
    {
        return "-Dwebdriver.chrome.driver=$driverPath";
    }

    protected function downloadChrome(string $os, string $chromeVersion, string $driverPath)
    {
        WLogger::logDebug($this, "Скачиваем chromedriver под $os для Google Chrome $chromeVersion");

        $chromedriverVersion = @file_get_contents($this->url . "/LATEST_RELEASE_$chromeVersion");

        if ($chromedriverVersion === false)
        {
            throw new GeneralException("Не удалось получить версию chromedriver для Google Chrome $chromeVersion");
        }

        if ($os === CurrentOS::LINUX)
        {
            $url = $this->url . "/$chromedriverVersion/chromedriver_linux64.zip";
        }

        if ($os === CurrentOS::MAC)
        {
            $url = $this->url . "/$chromedriverVersion/chromedriver_mac64.zip";
        }

        $zipfile = $this->outputDir . '/chromedriver_temp.zip';
        @unlink($zipfile);

        if (!@file_put_contents($zipfile, fopen($url, 'rb')))
        {
            throw new GeneralException("Не удалось скачать $url");
        }

        if (!copy('zip://' . $zipfile . '#chromedriver', $driverPath))
        {
            throw new GeneralException('Не удалось распаковать скачанный chromedriver');
        }

        unlink($zipfile);

        chmod($driverPath, 0775);
    }
}