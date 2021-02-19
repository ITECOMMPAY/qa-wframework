<?php


namespace Codeception\Module;


use Codeception\Lib\ModuleContainer;
use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\Archive;
use Codeception\Lib\WFramework\Helpers\MultiProcessLock;
use Codeception\Lib\WFramework\Helpers\System;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Module as CodeceptionModule;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;

class FFmpegManagerModule extends CodeceptionModule
{
    protected $ffmpegPath = '';

    protected $config = [
        'outputFolder'    => '.wffmpeg',
        'linuxBuildUrl'   => "https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz",
        'macBuildUrl'     => "https://evermeet.cx/ffmpeg/get/zip",
        'lock'            => 25459
    ];

    /**
     * @var MultiProcessLock
     */
    private $multiProcessLock;

    public function __construct(
        ModuleContainer $moduleContainer,
        $config = null
    )
    {
        parent::__construct($moduleContainer, $config);

        $this->multiProcessLock = new MultiProcessLock($this->config['lock']);
    }

    public function getFfmpegPath() : string
    {
        if (empty($this->ffmpegPath))
        {
            $this->ffmpegPath = $this->findFfmpeg();
        }

        return $this->ffmpegPath;
    }

    protected function findFfmpeg() : string
    {
        WLogger::logNotice($this, 'Получаем ffmpeg');

        if ($this->ffmpegInstalled())
        {
            return 'ffmpeg';
        }

        $this->multiProcessLock->lock([$this, 'ffmpegDownloaded']);

        $this->config['outputDir'] = System::mkDirInHome($this->config['outputFolder']);
        $binaryPath = $this->getBinaryPath();

        if ($this->ffmpegDownloaded())
        {
            WLogger::logDebug($this, 'ffmpeg уже скачан');
            $this->multiProcessLock->unlock();
            return $binaryPath;
        }

        $this->downloadFfmpeg();

        $this->multiProcessLock->unlock();

        return $binaryPath;
    }

    protected function getBinaryPath() : string
    {
        return $this->config['outputDir'] . '/ffmpeg';
    }

    protected function ffmpegInstalled() : bool
    {
        $proc = new Process(['which', 'ffmpeg']);
        $proc->run();
        $output = $proc->getOutput();

        return !empty($output);
    }

    public function ffmpegDownloaded() : bool
    {
        return file_exists($this->getBinaryPath());
    }

    protected function downloadFfmpeg()
    {
        $os = System::getOS();

        WLogger::logDebug($this, "Скачиваем ffmpeg под $os");

        if ($os === System::LINUX)
        {
            $this->downloadForLinux();
        }
        elseif ($os === System::MAC)
        {
            $this->downloadForMac();
        }
        else
        {
            throw new UsageException('Данный модуль работает только под GNU/Linux и Mac. Текущая ОС: ' . $os);
        }

        if (!$this->ffmpegDownloaded())
        {
            throw new GeneralException('После распаковки не найден исполняемый файл ffmpeg');
        }

        chmod($this->getBinaryPath(), 0775);
    }

    protected function downloadForLinux()
    {
        $url = $this->config['linuxBuildUrl'];

        $xzFile = $this->config['outputDir'] . '/ffmpeg-git-amd64-static.tar.xz';
        @unlink($xzFile);

        if (!@file_put_contents($xzFile, fopen($url, 'rb')))
        {
            throw new GeneralException("Не удалось скачать ffmpeg по URL: $url");
        }

        Archive::xzExtract($xzFile);

        $this->moveFfmpeg();

        unlink($xzFile);
    }

    protected function moveFfmpeg()
    {
        $it = new RecursiveDirectoryIterator($this->config['outputDir']);

        foreach(new RecursiveIteratorIterator($it) as $file)
        {
            if (basename($file) !== 'ffmpeg')
            {
                continue;
            }

            rename($file, $this->getBinaryPath());
            return;
        }

        throw new GeneralException("Не удалось найти распакованный ffmpeg в директории: {$this->config['outputDir']}");
    }

    protected function downloadForMac()
    {
        $url = $this->config['macBuildUrl'];

        $zipfile = $this->config['outputDir'] . '/ffmpeg.zip';
        @unlink($zipfile);

        if (!@file_put_contents($zipfile, fopen($url, 'rb')))
        {
            throw new GeneralException("Не удалось скачать $url");
        }

        Archive::zipExtract($zipfile);

        unlink($zipfile);
    }
}