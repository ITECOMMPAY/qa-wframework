<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\ArchiveException;
use PharData;
use Symfony\Component\Process\Process;
use ZipArchive;

class Archive
{
    /**
     * Разархивирует .zip архив
     *
     * @param string $file
     * @param string $outputDirectory
     * @param array $entries
     * @throws ArchiveException
     */
    public static function zipExtract(string $file, string $outputDirectory = '', array $entries = [])
    {
        $path = dirname($file);

        $zip = new ZipArchive;

        if (!$zip->open($file))
        {
            throw new ArchiveException('Не удалось открыть zip-архив');
        }

        if (!$zip->extractTo(empty($outputDirectory) ? $path : $outputDirectory, empty($entries) ? null : $entries))
        {
            throw new ArchiveException('Не удалось распаковать zip-архив: ' . $zip->getStatusString());
        }

        $zip->close();
    }

    /**
     * Разархивирует .tar, .tar.gz и .tar.bz2 архивы
     *
     * @param string $file
     * @param string $outputDirectory
     * @param array $entries
     * @throws ArchiveException
     */
    public static function pharExtract(string $file, string $outputDirectory = '', array $entries = [])
    {
        $path = dirname($file);
        $filenameParts = explode('.', basename($file));

        $arc = new PharData($file);

        if (array_pop($filenameParts) !== 'tar')
        {
            $tempFile = $path . '/' . implode('.', $filenameParts);
            @unlink($tempFile);
            $arc->decompress();
            static::pharExtract($tempFile, $outputDirectory, $entries);
            @unlink($tempFile);
            return;
        }

        if (!$arc->extractTo(empty($outputDirectory) ? $path : $outputDirectory, empty($entries) ? null : $entries, true))
        {
            throw new ArchiveException('Не удалось распаковать tar-архив');
        }
    }

    /**
     * Разархивирует .tar.lzma и .tar.xz архивы
     *
     * @param string $file
     * @param string $outputDirectory
     * @param array $entries
     */
    public static function xzExtract(string $file, string $outputDirectory = '', array $entries = [])
    {
        $proc = new Process(['xz', '-d', '-f', '-k', $file]);
        $proc->setTimeout(null);
        $proc->setIdleTimeout(null);
        $proc->run();

        if (!$proc->isSuccessful())
        {
            throw new ArchiveException('Не удалось распаковать xz-архив');
        }

        $path = dirname($file);
        $filenameParts = explode('.', basename($file));
        array_pop($filenameParts);
        $tempFile = $path . '/' . implode('.', $filenameParts);

        static::pharExtract($tempFile, $outputDirectory, $entries);
    }
}