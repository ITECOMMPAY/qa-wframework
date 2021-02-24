<?php


namespace Codeception\Lib\WFramework\Selenium\Managers;


use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Selenium\AbstractDriverManager;

class SeleniumServerManager extends AbstractDriverManager
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $outputDir;

    /** @var bool */
    protected $autoUpdate;

    public function __construct(array $config)
    {
        $this->url = $config['seleniumServerStandaloneUrl'];
        $this->outputDir = $config['outputDir'];
        $this->autoUpdate = $config['autoUpdateDrivers'];
    }

    public function get() : string
    {
        $filename = substr($this->url, strrpos($this->url, '/'));

        $jarLocalName = $this->outputDir . $filename;

        if (file_exists($jarLocalName))
        {
            WLogger::logDebug($this, 'Selenium Server Standalone уже скачан - используем его');
            return $this->asParam($jarLocalName);
        }

        if (!$this->autoUpdate)
        {
            throw new UsageException("autoUpdateDrivers = false, но Selenium Server Standalone $jarLocalName - отсутствует");
        }

        WLogger::logDebug($this, 'Скачиваем Selenium Server Standalone');

        if (!@file_put_contents($jarLocalName, fopen($this->url, 'rb')))
        {
            throw new GeneralException("Не удалось скачать $this->url");
        }

        return $this->asParam($jarLocalName);
    }

    protected function asParam(string $jarPath) : string
    {
        return "-jar $jarPath";
    }
}