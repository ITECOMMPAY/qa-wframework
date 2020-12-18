<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.02.19
 * Time: 18:02
 */

namespace Codeception\Lib\WFramework\Logger;

use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlFormatter;
use Codeception\Lib\WFramework\Logger\HtmlLogger\CustomHtmlStreamHandler;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebDriver;
use Monolog\Logger;
use function file_put_contents;
use function preg_replace;
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

    /**
     * Creates if not created and returns the Doctrine entity manager with the given parameters.
     * If the provided database schema doesn't exist - creates it.
     * @param string $DBMS - database location from settings.yml
     * @param string $dbName - schema name
     * @param string $mappingDir - directory with Doctrine mappings for the given schema
     * @return mixed
     */
    public static function get() : Log
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    protected function __construct()
    {
        $this->logger = new Logger('WLogger');
    }

    /** @var WLogger */
    protected $logger;


    protected $screenshotsDirectory = '';

    /** @var bool */
    protected $debug = true;

    /** @var bool */
    protected $takeScreenshots = true;

    /** @var int */
    protected $currentScreenshotNumber = 0;

    /** @var ProxyWebDriver */
    protected $proxyWebDriver;

    // Здесь кончается стандартный код синглтона

    public function setOutputFile(string $fullName, string $screenshotsDir)
    {
        $this->screenshotsDirectory = $screenshotsDir;

        $this->currentScreenshotNumber = 0;

        $logHandler = new CustomHtmlStreamHandler($fullName, Logger::DEBUG);
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

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function addEmergency($message, array $context = array())
    {
        if ($this->debug)
        {
            echo $message . PHP_EOL;
        }

        $context['screenshot_filename'] = $this->getScreenshot($context);

        $this->logger->emergency($message, $context);
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
    public function addAlert($message, array $context = array())
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
    public function addCritical($message, array $context = array())
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
    public function addError($message, array $context = array())
    {
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
    public function addWarning($message, array $context = array())
    {
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
    public function addNotice($message, array $context = array())
    {
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
    public function addInfo($message, array $context = array())
    {
        if ($this->debug)
        {
            echo preg_replace('/^/m', '        ', $message) . PHP_EOL;
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
    public function addDebug($message, array $context = array())
    {
        if ($this->debug)
        {
            echo preg_replace('/^/m', '                ', $message) . PHP_EOL;
        }

        $this->logger->debug($message, $context);
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
}
