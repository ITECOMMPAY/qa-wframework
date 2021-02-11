<?php


namespace Codeception\Lib\WFramework\Selenium\Managers;


use Codeception\Lib\WFramework\Exceptions\FrameworkStaledException;
use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\CurrentOS;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Selenium\AbstractDriverManager;
use PharData;

class GeckoDriverManager extends AbstractDriverManager
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $outputDir;

    /** @var bool */
    protected $autoUpdate;

    public function __construct(array $config)
    {
        $this->url = $config['geckoDriverUrl'];
        $this->outputDir = $config['outputDir'];
        $this->autoUpdate = $config['autoUpdateDrivers'];
    }

    public function get() : string
    {
        $os = $this->getOS();

        $firefoxVersion = $this->getFirefoxVersion();

        if (empty($firefoxVersion))
        {
            WLogger::logDebug($this, 'Не удалось получить версию Mozilla Firefox - считаем, что он не установлен');
            return '';
        }

        $driverLocalName = $this->outputDir . "/geckodriver_{$os}_{$firefoxVersion}";

        if (file_exists($driverLocalName))
        {
            WLogger::logDebug($this, 'geckodriver уже скачан - используем его');
            return $this->asParam($driverLocalName);
        }

        if (!$this->autoUpdate)
        {
            throw new UsageException("autoUpdateDrivers = false, но драйвер $driverLocalName - отсутствует");
        }

        $this->downloadFirefox($os, $firefoxVersion, $driverLocalName);

        return $this->asParam($driverLocalName);
    }

    protected function getFirefoxVersion() : string
    {
        WLogger::logDebug($this, 'Получаем версию Mozilla Firefox');

        ['version' => $firefoxVersion] = $this->getBrowserInfo(['firefox', 'iceweasel'], '--version', '%\s+(\d+).\d+%m');

        if (empty($firefoxVersion))
        {
            return '';
        }

        WLogger::logDebug($this, "Версия Mozilla Firefox: $firefoxVersion");

        return $firefoxVersion;
    }

    protected function downloadFirefox(string $os, string $firefoxVersion, string $driverPath)
    {
        WLogger::logDebug($this, "Скачиваем geckodriver под $os для Mozilla Firefox $firefoxVersion");

        $url = $this->getDownloadUrl($os);

        $tarFile = $this->outputDir . '/geckodriver_temp.tar';
        @unlink($tarFile);

        $gzFile = $tarFile . '.gz';

        if (!@file_put_contents($gzFile, fopen($url, 'rb')))
        {
            throw new GeneralException("Не удалось скачать geckodriver по URL: $url");
        }

        $gz = new PharData($gzFile);
        $gz->decompress();

        $tar = new PharData($tarFile);
        if (!$tar->extractTo($this->outputDir, 'geckodriver', true))
        {
            throw new GeneralException('Не удалось распаковать скачанный geckodriver');
        }

        $temp = realpath($this->outputDir . '/geckodriver');

        if (!$temp)
        {
            throw new GeneralException('Не удалось распаковать скачанный geckodriver');
        }

        rename($temp, $driverPath);

        unlink($gzFile);
        unlink($tarFile);

        chmod($driverPath, 0775);
    }

    protected function getDownloadUrl(string $os) : string
    {
        $arrContextOptions = [
            'http' => [
                'user_agent' => 'Some/1.0 downloader'
            ]
        ];

        $latestReleaseInfo = @file_get_contents($this->url . '/latest', false, stream_context_create($arrContextOptions));

        if ($latestReleaseInfo === false)
        {
            throw new GeneralException('Не удалось установить версию последнего geckodriver с github');
        }

        $info = json_decode($latestReleaseInfo, true);

        if (!isset($info['assets'][0]['browser_download_url']))
        {
            throw new FrameworkStaledException('Формат ответа от github изменился');
        }

        if ($os === CurrentOS::LINUX)
        {
            $needle = 'linux64';
        }

        if ($os === CurrentOS::MAC)
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
            throw new GeneralException("Не удалось найти последний geckodriver для $needle на github");
        }

        return $url;
    }

    protected function asParam(string $driverPath) : string
    {
        return "-Dwebdriver.gecko.driver=$driverPath";
    }
}