<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 18:02
 */

namespace Codeception\Lib\WFramework\Logger;

use Codeception\Lib\WFramework\FFmpegBin\FFmpeg;
use Codeception\Lib\WFramework\Helpers\ClassHelper;
use Codeception\Lib\WFramework\Helpers\Composite;
use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlFormatter;
use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlStreamHandler;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\Steps\StepsGroup;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebDriver;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Monolog\Logger;
use function file_put_contents;
use function sprintf;

class Log
{
    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton!');
    }

    public static function get() : Log
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    // Здесь кончается стандартный код синглтона

    protected function __construct()
    {
        $this->logger = new Logger('WLogger');

        $this->ffmpeg = new FFmpeg();
    }

    /** @var Logger */
    protected $logger;

    /** @var FFmpeg */
    protected $ffmpeg;

    /** @var string */
    protected $logDirectory = '';

    /** @var string */
    protected $logFilename = '';

    /** @var string */
    protected $screenshotsDirectory = '';

    /** @var bool */
    protected $debug = true;

    /** @var bool */
    protected $takeScreenshots = true;

    /** @var int */
    protected $currentScreenshotNumber = 0;

    /** @var bool */
    protected $screenshotsToVideo = true;

    /** @var ProxyWebDriver */
    protected $proxyWebDriver;

    public function setOutputFile(string $logDir, string $fullName, string $screenshotsDir)
    {
        if (!is_dir($logDir))
        {
            mkdir($logDir, 0777, true);
        }

        if (!file_exists($fullName))
        {
            touch($fullName);
        }

        if (!is_dir($screenshotsDir))
        {
            mkdir($screenshotsDir, 0777, true);
        }

        $this->logDirectory = realpath($logDir);
        $this->logFilename = realpath($fullName);
        $this->screenshotsDirectory = realpath($screenshotsDir);

        $this->currentScreenshotNumber = 0;

        $logHandler = new CustomHtmlStreamHandler(realpath($this->logFilename), Logger::DEBUG);
        $logFormatter = new CustomHtmlFormatter(DATE_ATOM);
        $logHandler->setFormatter($logFormatter);

        $this->logger->setHandlers([$logHandler]);
    }

    public function setDebug($value)
    {
        $this->debug = $value;
    }

    public function setWebDriver(ProxyWebDriver $proxyWebDriver)
    {
        $this->proxyWebDriver = $proxyWebDriver;
    }

    public function setTakeScreenshots(bool $value)
    {
        $this->takeScreenshots = $value;
    }

    public function setScreenshotsToVideo(bool $screenshotsToVideo)
    {
        $this->screenshotsToVideo = $screenshotsToVideo;
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
        if (!$this->takeScreenshots || !$this->proxyWebDriver->initialized() || $this->screenshotsDirectory === '')
        {
            return '';
        }

        $screenshotName = $this->getScreenshotName();

        $filename = $this->screenshotsDirectory . '/' . $screenshotName . '.png';

        $this->currentScreenshotNumber++;

        $this->proxyWebDriver->takeScreenshot($filename);

        return $screenshotName;
    }

    protected function getScreenshotName() : string
    {
        return sprintf('%08d', $this->currentScreenshotNumber);
    }

    public function finalizeLog()
    {
        if ($this->takeScreenshots && $this->screenshotsToVideo && $this->ffmpeg->videoFromPNGs($this->screenshotsDirectory, "$this->screenshotsDirectory/video") && file_exists("$this->screenshotsDirectory/video.mp4"))
        {
            $this->addDebug($this, 'Скриншоты были успешно преобразованы в видео');

            $files = array_filter(scandir($this->screenshotsDirectory), function ($file) {return strlen($file) === 12 && strpos($file, '.png', -4) !== false;});

            array_pop($files);

            $this->addDebug($this, 'Удаляем все скриншоты кроме последнего');

            foreach ($files as $file)
            {
                unlink("$this->screenshotsDirectory/$file");
            }
        }

        TestProperties::save($this->logDirectory . '/test_properties.json');

        echo PHP_EOL . 'Ссылка на HTML-лог прогона: ' . PHP_EOL . "file://$this->logFilename" . PHP_EOL;
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addAssertHard(string $message, array $context = array())
    {
        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addAssertSoft(string $message, array $context = array())
    {
        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addError($object, string $message, array $context = array())
    {
        $message = $this->formatMessage($object, $message);

        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addWarning($object, string $message, array $context = array())
    {
        $message = $this->formatMessage($object, $message);

        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addNotice($object, string $message, array $context = array())
    {
        $message = $this->formatMessage($object, $message);

        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addInfo($object, string $message, array $context = array())
    {
        $message = $this->formatMessage($object, $message);

        if ($this->debug)
        {
            echo  '        ' . $message . PHP_EOL;
        }

        if ($this->debug)
        {
            $context['screenshot_filename'] = $this->getScreenshot($context);
        }

        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addDebug($object, string $message, array $context = array())
    {
        $message = $this->formatMessage($object, $message);

        if ($this->debug)
        {
            echo '                ' . $message . PHP_EOL;
        }

        $this->logger->debug($message, $context);
    }

    public function addSmart($object, string $message, array $context = [])
    {
        if ($object instanceof StepsGroup)
        {
            $this->addNotice($object, $message, $context);
            return;
        }

        if (!$object instanceof IPageObject)
        {
            $this->addDebug($object, $message, $context);
            return;
        }

        if ($object instanceof WBlock)
        {
            $this->addInfo($object, $message, $context);
            return;
        }

        $parent = $object->getParent();

        if ($parent instanceof WBlock && $parent->hasChild($object->getName()))
        {
            $this->addInfo($object, $message, $context);
            return;
        }

        $this->addDebug($object, $message, $context);
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
