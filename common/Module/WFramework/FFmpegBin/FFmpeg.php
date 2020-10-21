<?php


namespace Common\Module\WFramework\FFmpegBin;

use Common\Module\WFramework\Helpers\CurrentOS;
use Common\Module\WFramework\Exceptions\SeleniumServerModule\UsageException;
use Common\Module\WFramework\Logger\WLogger;
use function realpath;
use Symfony\Component\Process\Process;

class FFmpeg
{
    protected $binPath = 'ffmpeg';

    public function __construct()
    {
        $this->binPath = $this->getBinPath();
    }

    protected function getBinPath() : string
    {
        if (!empty(shell_exec('which ffmpeg')))
        {
            return 'ffmpeg';
        }

        $os = CurrentOS::get();

        switch ($os)
        {
            case CurrentOS::LINUX:
                return realpath(__DIR__ . '/ffmpeg_linux');

            case CurrentOS::MAC:
                return realpath(__DIR__ . '/ffmpeg_mac');

            default:
                throw new UsageException('Данный модуль работает только под GNU/Linux и Mac. Текущая ОС: ' . $os);
        }
    }

    public function videoFromPNGs(string $inputDir, string $outputFile) : bool
    {
        WLogger::logDebug('Преобразуем скриншоты в видео');

        $cmd = "$this->binPath -framerate 1 -i $inputDir/%08d.png -c:v libx264 -tune stillimage -movflags +faststart -vf fps=1 -pix_fmt yuv422p $outputFile.mp4";

        WLogger::logDebug('Команда для запуска: ' . $cmd);

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();

        return $process->isSuccessful();
    }
}
