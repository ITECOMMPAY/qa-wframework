<?php


namespace Codeception\Module;


use function array_map;
use function array_pop;
use function codecept_data_dir;
use Codeception\Module as CodeceptionModule;
use Codeception\Lib\WFramework\Exceptions\GeneralException;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function glob;
use function hash;
use function implode;
use function is_dir;
use function mb_strcut;
use function md5;
use function min;
use function mkdir;
use function pathinfo;
use function sprintf;
use function str_replace;
use \Aws\S3\S3MultiRegionClient as AwsS3MultiRegionClient;
use function trim;
use function unlink;


class ShotsStorageModule extends CodeceptionModule
{
    protected $config = [
        'version'    => 'latest',
        'region'     => 'eu-central-1',
        'accessKey'  => 'accessKey',
        'secretKey'  => 'secretKey',
        'bucket'     => 'bucket',
        'source'     => 'local'
    ];

    protected $requiredFields = ['bucket', 'accessKey', 'secretKey', 'source'];

    protected $shotsDir = '';

    protected $tempDir = '';

    protected $localFilemap = [];

    protected $remoteFilemap = [];

    protected $tempFilemap = [];

    /** @var AwsS3MultiRegionClient */
    protected $s3Client;

    public function _initialize()
    {
        WLogger::logNotice($this, 'Инициализируем хранилище скриншотов');

        $this->shotsDir = codecept_data_dir() . '/shots/';

        if (!is_dir($this->shotsDir))
        {
            if (!mkdir($concurrentDirectory = $this->shotsDir, 0777, true) && !is_dir($concurrentDirectory))
            {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            file_put_contents($this->shotsDir . '.gitignore', '**' . PHP_EOL);

            return;
        }

        $this->tempDir = codecept_data_dir() . '/shots/temp/';

        if (!is_dir($this->tempDir))
        {
            if (!mkdir($concurrentDirectory = $this->tempDir, 0777, true) && !is_dir($concurrentDirectory))
            {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }

            file_put_contents($this->tempDir . '.gitignore', '**' . PHP_EOL);

            return;
        }

        $this->fillLocalFilemap();
        $this->fillLocalTempFilemap();

        if ($this->config['source'] === 'remote')
        {
            $this->initializeS3Client();
            $this->fillRemoteFilemap();
        }
    }

    protected function initializeS3Client()
    {
        WLogger::logDebug($this, 'Инициализируем клиент S3');

        $this->s3Client = new AwsS3MultiRegionClient(
            [
                'version' => $this->config['version'],
                'region'  => $this->config['region'],
                'credentials' => [
                    'key'    => $this->config['accessKey'],
                    'secret' => $this->config['secretKey'],
                ],
                'calculate_md5' => true,
            ]
        );

        try
        {
            $accessResponse = $this->s3Client->headBucket(
                [
                    'Bucket' => $this->config['bucket']
                ]
            );
        }
        catch (\Aws\S3\Exception\S3Exception $e)
        {
            throw new UsageException('Не получается подключиться к бакету: ' .
                                     $this->config['bucket'] . ' - исключение S3: ' . PHP_EOL . $e->getMessage());
        }

        $responseData = $accessResponse->toArray();

        if (!isset($responseData['@metadata']['statusCode']) || $responseData['@metadata']['statusCode'] !== 200)
        {
            throw new UsageException('Не получается подключиться к бакету: ' .
                                     $this->config['bucket'] . ' - ответ S3: ' . PHP_EOL . json_encode($responseData,JSON_PRETTY_PRINT));
        }
    }

    protected function parseName(string $name) : array
    {
        $name = pathinfo($name, PATHINFO_FILENAME);

        $parsedName = explode('_', $name);

        if (count($parsedName) !== 2)
        {
            return [];
        }

        return ['shortName' => $parsedName[0], 'contentMD5' => $parsedName[1]];
    }

    protected function fillLocalFilemap()
    {
        WLogger::logDebug($this, 'Читаем имена локальных скриншотов');

        foreach (glob($this->shotsDir . '*.png', GLOB_NOSORT) as $filename)
        {
            $parsedName = $this->parseName($filename);

            if (empty($parsedName))
            {
                continue;
            }

            ['shortName' => $shortName, 'contentMD5' => $contentMD5] = $parsedName;

            $this->localFilemap[$shortName] = $contentMD5;
        }
    }

    protected function fillRemoteFilemap()
    {
        WLogger::logDebug($this, 'Читаем имена удалённых скриншотов');

        $truncated = false;
        $continuationToken = '';

        $config = [
            'Bucket' => $this->config['bucket']
        ];

        do
        {
            if ($truncated)
            {
                $config['ContinuationToken'] = $continuationToken;
            }

            $objects = $this->s3Client->listObjectsV2(
                $config
            );

            $data = $objects->toArray();

            foreach ($data['Contents'] as $file)
            {
                $parsedName = $this->parseName($file['Key']);

                if (empty($parsedName))
                {
                    continue;
                }

                ['shortName' => $shortName, 'contentMD5' => $contentMD5] = $parsedName;

                $this->remoteFilemap[$shortName] = $contentMD5;
            }

            $truncated = $data['IsTruncated'];
            $continuationToken = $data['NextContinuationToken'] ?? '';
        }
        while ($truncated);
    }

    public function putShot(string $name, string $rawPNG)
    {
        WLogger::logDebug($this, 'Сохраняем скриншот локально: ' . $name);

        $shortName = $this->getShortName($name);
        $contentMD5 = md5($rawPNG);

        $this->removeLocalShot($shortName);

        $filename = $this->shotsDir . $shortName . '_' . $contentMD5 . '.png';
        file_put_contents($filename, $rawPNG);
        $this->localFilemap[$shortName] = $contentMD5;
    }

    protected function removeLocalShot(string $shortName)
    {
        if (!isset($this->localFilemap[$shortName]))
        {
            return;
        }

        WLogger::logDebug($this, 'Удаляем локальный скриншот: ' . $shortName);

        $filename = $this->shotsDir . $shortName . '_' . $this->localFilemap[$shortName] . '.png';

        unlink($filename);

        unset($this->localFilemap[$shortName]);
    }

    protected function removeRemoteShot(string $shortName)
    {
        if (!isset($this->remoteFilemap[$shortName]))
        {
            return;
        }

        WLogger::logDebug($this, 'Удаляем удалённый скриншот: ' . $shortName);

        $filename = $shortName . '_' . $this->remoteFilemap[$shortName] . '.png';

        $deleteResult = $this->s3Client->deleteObject([
                                                    'Bucket' => $this->config['bucket'],
                                                    'Key' => $filename,
                                                ]);

        unset($this->remoteFilemap[$shortName]);
    }

    public function getShot(string $name) : string
    {
        switch ($this->config['source'])
        {
            case 'local':
                return $this->getLocalShot($name);

            case 'remote':
                return $this->getRemoteShot($name);
        }

        throw new UsageException("source должен быть 'local' или 'remote'");
    }

    protected function getLocalShot(string $name) : string
    {
        WLogger::logDebug($this, 'Получаем локальный скриншот: ' . $name);

        $shortName = $this->getShortName($name);

        if (!isset($this->localFilemap[$shortName]))
        {
            throw new UsageException("Скриншот не найден локально: $name (short: $shortName)");
        }

        $filename = $this->shotsDir . $shortName . '_' . $this->localFilemap[$shortName] . '.png';

        return file_get_contents($filename);
    }

    protected function getRemoteShot(string $name) : string
    {
        WLogger::logDebug($this, 'Получаем удалённый скриншот: ' . $name);

        $shortName = $this->getShortName($name);

        if (!isset($this->remoteFilemap[$shortName]))
        {
            throw new UsageException("Скриншот не найден удалённо: $name (short: $shortName)");
        }

        $contentMD5 = $this->remoteFilemap[$shortName];

        $filename = $shortName . '_' . $contentMD5 . '.png';

        if (!isset($this->localFilemap[$shortName]) || $this->localFilemap[$shortName] !== $contentMD5)
        {
            $getResult = $this->s3Client->getObject([
                                                        'Bucket' => $this->config['bucket'],
                                                        'Key' => $filename,
                                                        'SaveAs' => $this->shotsDir . $filename
                                                    ]);

            $this->removeLocalShot($shortName);
            $this->localFilemap[$shortName] = $contentMD5;
        }

        return file_get_contents($this->shotsDir . $filename);
    }

    public function uploadShots()
    {
        WLogger::logDebug($this, 'Загружаем скриншоты в S3');

        if ($this->config['source'] === 'local')
        {
            $this->initializeS3Client();
            $this->fillRemoteFilemap();
        }

        foreach ($this->localFilemap as $shortName => $contentMD5)
        {
            if (isset($this->remoteFilemap[$shortName]) && $this->remoteFilemap[$shortName] === $contentMD5)
            {
                continue;
            }

            $filename = $shortName . '_' . $contentMD5 . '.png';

            $putResult = $this->s3Client->putObject([
                                           'Bucket' => $this->config['bucket'],
                                           'Key' => $filename,
                                           'SourceFile' => $this->shotsDir . $filename
                                       ]);

            $this->removeRemoteShot($shortName);
            $this->remoteFilemap[$shortName] = $contentMD5;
        }
    }

    protected function fillLocalTempFilemap()
    {
        WLogger::logDebug($this, 'Читаем имена временных скриншотов');

        foreach (glob($this->tempDir . '*.png', GLOB_NOSORT) as $filename)
        {
            $parsedName = $this->parseName($filename);

            if (empty($parsedName))
            {
                continue;
            }

            ['shortName' => $shortName, 'contentMD5' => $contentMD5] = $parsedName;

            $this->tempFilemap[$shortName] = $contentMD5;
        }
    }

    public function putTempShot(string $name, string $rawPNG)
    {
        WLogger::logDebug($this, 'Сохраняем временный скриншот: ' . $name);

        $shortName = $this->getShortName($name);
        $contentMD5 = md5($rawPNG);

        $this->removeTempShot($shortName);

        $filename = $this->tempDir . $shortName . '_' . $contentMD5 . '.png';
        file_put_contents($filename, $rawPNG);
        $this->tempFilemap[$shortName] = $contentMD5;
    }

    protected function removeTempShot(string $shortName)
    {
        if (!isset($this->tempFilemap[$shortName]))
        {
            return;
        }

        WLogger::logDebug($this, 'Удаляем временный скриншот: ' . $shortName);

        $filename = $this->tempDir . $shortName . '_' . $this->tempFilemap[$shortName] . '.png';

        unlink($filename);

        unset($this->tempFilemap[$shortName]);
    }

    public function acceptTempShots()
    {
        WLogger::logDebug($this, 'Перемещаем временные скриншоты в локальные');

        foreach ($this->tempFilemap as $shortName => $contentMD5)
        {
            if (isset($this->localFilemap[$shortName]) && $this->localFilemap[$shortName] === $contentMD5)
            {
                continue;
            }

            $this->removeLocalShot($shortName);

            $tempFilename = $this->tempDir . $shortName . '_' . $contentMD5 . '.png';
            $localFilename = $this->shotsDir . $shortName . '_' . $contentMD5 . '.png';

            if (!copy($tempFilename, $localFilename))
            {
                throw new GeneralException("Не удалось скопировать $tempFilename в $localFilename");
            }

            $this->removeTempShot($shortName);

            $this->localFilemap[$shortName] = $contentMD5;
        }
    }

    public function getShortName(string $fullPOName) : string
    {
        $nameHash = hash('crc32b', $fullPOName);

        $nameParts = array_map(function ($el) { return trim($el); }, explode('/', $fullPOName));

        $poName = array_pop($nameParts);

        if (mb_strlen($poName, '8bit') >= 64)
        {
            $poName = trim(mb_strcut($poName, 0, 48)) . '[...]' . trim(mb_strcut($poName, -8));
        }

        $shortenedPath = implode('+', array_map(function ($el) { return mb_substr($el, 0, min(3, mb_strlen($el))); }, $nameParts));

        if (mb_strlen($shortenedPath, '8bit') >= 128)
        {
            $shortenedPath = trim(mb_strcut($shortenedPath, 0, 56)) . '[...]' . trim(mb_strcut($shortenedPath, -56));
        }

        return str_replace([' ', '_'], '-', $shortenedPath . '+' . $poName . "[$nameHash]");
    }
}
