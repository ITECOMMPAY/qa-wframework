<?php


namespace Codeception\Module;


use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Lib\ModuleContainer;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlFormatter;
use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlStreamHandler;
use Codeception\Lib\WFramework\Logger\ILoggerModule;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Module as CodeceptionModule;
use Codeception\TestInterface;
use Monolog\Logger;
use Symfony\Component\Process\Process;

class HtmlLoggerModule extends CodeceptionModule implements ILoggerModule, DependsOnModule
{
    /** @var Logger */
    protected $logger;

    /** @var WebTestingModule */
    protected $webTestingModule;

    /** @var FFmpegManagerModule */
    protected $ffmpegManagerModule;

    /** @var int */
    protected $currentScreenshotNumber = 0;

    /** @var string */
    protected $ffmpegPath = '';

    /** @var string */
    protected $logDirectory = '';

    /** @var string */
    protected $logFilename = '';

    /** @var string */
    protected $screenshotsDirectory = '';

    protected $config = [
        'debug'              => true,
        'takeScreenshots'    => true,
        'screenshotsToVideo' => true,
    ];

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
            WebTestingModule::class    => $this->dependencyMessage,
            FFmpegManagerModule::class => $this->dependencyMessage,
        ];
    }

    public function _inject(WebTestingModule $webTestingModule, FFmpegManagerModule $ffmpegManagerModule)
    {
        $this->webTestingModule = $webTestingModule;
        $this->ffmpegManagerModule = $ffmpegManagerModule;
    }

    protected function getWebTestingModule() : WebTestingModule
    {
        return $this->webTestingModule;
    }

    protected function getFFmpegManagerModule() : FFmpegManagerModule
    {
        return $this->ffmpegManagerModule;
    }

    public function __construct(
        ModuleContainer $moduleContainer,
        $config = null
    )
    {
        parent::__construct($moduleContainer, $config);

        $this->logger = new Logger('WLogger');
    }

    public function _initialize()
    {
        WLogger::logNotice($this, 'Подключаем HTML-логи');

        WLogger::setLoggerModule($this);

        if ($this->config['takeScreenshots'] && $this->config['screenshotsToVideo'])
        {
            $this->ffmpegPath = $this->getFFmpegManagerModule()->getFfmpegPath();
        }
    }

    public function _before(TestInterface $test)
    {
        $this->beginLog($test);
    }

    public function _after(TestInterface $test)
    {
        $this->endLog();
    }

    protected function beginLog($test)
    {
        $cestName = pathinfo($test->getMetadata()->getFilename(), PATHINFO_FILENAME);
        $testName = $test->getMetadata()->getName();
        $time = (new \DateTime())->format('Y-m-d\TH-i-s');

        $this->setLogFile($time . '__' . $cestName . '__' . $testName);
    }

    protected function endLog()
    {
        $this->finalizeLog();

        $this->setLogFile();
    }

    protected function setLogFile(string $filename = '')
    {
        if (empty($filename))
        {
            $time = (new \DateTime())->format('Y-m-d');
            $filename = 'defaultWebLog' . '__' . $time;
        }

        $logDir = codecept_output_dir() . "/$filename";

        $logFilename = $logDir . "/$filename.html";

        $screenshotsDir = $logDir . '/screenshots';

        if (!is_dir($logDir))
        {
            mkdir($logDir, 0777, true);
        }

        if (!file_exists($logFilename))
        {
            touch($logFilename);
        }

        if (!is_dir($screenshotsDir))
        {
            mkdir($screenshotsDir, 0777, true);
        }

        $this->logDirectory = realpath($logDir);
        $this->logFilename = realpath($logFilename);
        $this->screenshotsDirectory = realpath($screenshotsDir);

        $this->currentScreenshotNumber = 0;

        $logHandler = new CustomHtmlStreamHandler(realpath($this->logFilename), Logger::DEBUG);
        $logFormatter = new CustomHtmlFormatter(DATE_ATOM);
        $logHandler->setFormatter($logFormatter);

        $this->logger->setHandlers([$logHandler]);
    }

    protected function getScreenshot(array $context) : string
    {
        if (isset($context['screenshot_blob']))
        {
            return $this->saveScreenshot($context['screenshot_blob']);
        }

        return $this->takeScreenshot();
    }

    protected function saveScreenshot(string $imageBlob) : string
    {
        $screenshotName = $this->getScreenshotName();

        $filename = $this->screenshotsDirectory . '/' . $screenshotName . '.png';

        file_put_contents($filename, $imageBlob);

        $this->currentScreenshotNumber++;

        return $screenshotName;
    }

    protected function takeScreenshot() : string
    {
        if (!$this->config['takeScreenshots'] || $this->screenshotsDirectory === '')
        {
            return '';
        }

        $webDriver = $this->getWebTestingModule()->getWebDriver();

        if (!$webDriver->initialized())
        {
            return '';
        }

        $screenshotName = $this->getScreenshotName();

        $filename = $this->screenshotsDirectory . '/' . $screenshotName . '.png';

        $this->currentScreenshotNumber++;

        $webDriver->takeScreenshot($filename);

        return $screenshotName;
    }

    protected function getScreenshotName() : string
    {
        return sprintf('%08d', $this->currentScreenshotNumber);
    }

    protected function screenshotsToVideo()
    {
        if (empty($this->ffmpegPath))
        {
            return;
        }

        $this->logNotice($this, 'Преобразуем скриншоты в видео');

        $inputDir = $this->screenshotsDirectory;
        $outputFile = "$this->screenshotsDirectory/video";

        $cmd = "{$this->ffmpegPath} -framerate 1 -i $inputDir/%08d.png -c:v libx264 -tune stillimage -movflags +faststart -vf fps=1 -pix_fmt yuv422p $outputFile.mp4";

        $this->logDebug($this, 'Команда для запуска: ' . $cmd);

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();

        if (!$process->isSuccessful() || !file_exists("$this->screenshotsDirectory/video.mp4"))
        {
            $this->logWarning($this, 'Не получилось преобразовать скриншоты в видео: ' . $process->getErrorOutput());
            return;
        }

        $files = array_filter(scandir($this->screenshotsDirectory), function ($file) {return strlen($file) === 12 && strpos($file, '.png', -4) !== false;});

        array_pop($files);

        $this->logDebug($this, 'Удаляем все скриншоты кроме последнего');

        foreach ($files as $file)
        {
            unlink("$this->screenshotsDirectory/$file");
        }
    }

    protected function finalizeLog()
    {
        $this->screenshotsToVideo();

        TestProperties::save($this->logDirectory . '/test_properties.json');

        echo PHP_EOL . 'Ссылка на HTML-лог прогона: ' . PHP_EOL . "file://$this->logFilename" . PHP_EOL;
    }



    public function logAssertHard(string $message, array $context = [])
    {
        if ($this->config['debug'])
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->alert($message, $context);
    }

    public function logAssertSoft(string $message, array $context = [])
    {
        if ($this->config['debug'])
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->critical($message, $context);
    }

    public function logError($object, string $message, array $context = [])
    {
        $message = $this->formatMessage($object, $message);

        if ($this->config['debug'])
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->error($message, $context);
    }

    public function logWarning($object, string $message, array $context = [])
    {
        $message = $this->formatMessage($object, $message);

        if ($this->config['debug'])
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->warning($message, $context);
    }

    public function logNotice($object, string $message, array $context = [])
    {
        $message = $this->formatMessage($object, $message);

        if ($this->config['debug'])
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->notice($message, $context);
    }

    public function logInfo($object, string $message, array $context = [])
    {
        $message = $this->formatMessage($object, $message);

        if ($this->config['debug'])
        {
            echo  '        ' . $message . PHP_EOL;
        }

        if ($this->config['debug'])
        {
            $context['screenshot_filename'] = $this->getScreenshot($context);
        }

        $this->logger->info($message, $context);
    }

    public function logDebug($object, string $message, array $context = [])
    {
        $message = $this->formatMessage($object, $message);

        if ($this->config['debug'])
        {
            echo '                ' . $message . PHP_EOL;
        }

        $this->logger->debug($message, $context);
    }

    public function logAction($object, string $message, array $context = [])
    {
        if ($object instanceof StepsGroup)
        {
            $this->logNotice($object, $message, $context);
            return;
        }

        if (!$object instanceof IPageObject)
        {
            $this->logDebug($object, $message, $context);
            return;
        }

        if ($object instanceof WBlock)
        {
            $this->logInfo($object, $message, $context);
            return;
        }

        $parent = $object->getParent();

        if ($parent instanceof WBlock && $parent->hasChild($object->getName()))
        {
            $this->logInfo($object, $message, $context);
            return;
        }

        $this->logDebug($object, $message, $context);
    }

    protected function formatMessage($object, string $message) : string
    {
        if ($object instanceof IPageObject)
        {
            $name = (string) $object;
        }
        else
        {
            $name = ClassHelper::getShortName(get_class($object));
        }

        return "$name -> $message";
    }
}