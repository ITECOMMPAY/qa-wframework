<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 27.03.19
 * Time: 15:36
 */

namespace Codeception\Module;

use Codeception\Lib\WFramework\Properties\TestProperties;
use function array_filter;
use function codecept_output_dir;
use Codeception\Module as CodeceptionModule;
use Codeception\TestInterface;
use Codeception\Lib\WFramework\FFmpegBin\FFmpeg;
use Codeception\Lib\WFramework\Logger\Log;
use Codeception\Lib\WFramework\ProxyWebDriver\ProxyWebDriver;
use function is_dir;
use function mkdir;
use function realpath;
use function scandir;
use function unlink;

/**
 * Данный модуль служит для обращения к логу из тестов Codeception.
 *
 * Для обращения к логу из кода фреймворка следует использовать статические методы класса WLogger.
 *
 * @package Common\Module\WFramework\Modules
 */
class LoggerModule extends CodeceptionModule
{
    /** @var FFmpeg */
    protected $ffmpeg;

    /** @var string */
    protected $logDir = '';

    /** @var string */
    protected $logFilePath = '';

    /** @var string */
    protected $screenshotsDir = '';

    protected $takeScreenshots = false;

    protected $screenshotsToVideo = false;

    public function _initialize()
    {
        $this->ffmpeg = new FFmpeg();
    }

    public function _setOutputFile(string $filename = 'defaultWebLog') : LoggerModule
    {
        if (empty($filename))
        {
            $time = (new \DateTime())->format('Y-m-d\TH-i-s.u');
            $filename = $time . '__' . bin2hex(random_bytes(80));
        }

        $logDir = codecept_output_dir() . "/$filename";

        $logFilePath = $logDir . "/$filename.html";

        $screenshotsDir = $logDir . '/screenshots';

        if (!is_dir($screenshotsDir))
        {
            mkdir($screenshotsDir, 0777, true);
        }

        if (!file_exists($logFilePath))
        {
            touch($logFilePath);
        }

        $this->logDir = realpath($logDir);
        $this->logFilePath = realpath($logFilePath);
        $this->screenshotsDir = realpath($screenshotsDir);

        Log::get()->setOutputFile($this->logFilePath, $this->screenshotsDir);

        return $this;
    }

    public function _setDebug(bool $value) : LoggerModule
    {
        Log::get()->setDebug($value);

        return $this;
    }

    public function _setWebDriver(ProxyWebDriver $proxyWebDriver) : LoggerModule
    {
        Log::get()->setWebDriver($proxyWebDriver);

        return $this;
    }

    public function _setTakeScreenshots(bool $value) : LoggerModule
    {
        Log::get()->setTakeScreenshots($value);

        $this->takeScreenshots = $value;

        return $this;
    }

    public function _setScreenshotsToVideo(bool $value) : LoggerModule
    {
        $this->screenshotsToVideo = $value;

        return $this;
    }

    /**
     * **HOOK** executed before test
     *
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        $cestName = pathinfo($test->getMetadata()->getFilename(), PATHINFO_FILENAME);
        $testName = $test->getMetadata()->getName();
        $time = (new \DateTime())->format('Y-m-d\TH-i-s');

        $this->_setOutputFile($time . '__' . $cestName . '__' . $testName);
    }

    /**
     * **HOOK** executed after test
     *
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        if ($this->takeScreenshots && $this->screenshotsToVideo && $this->ffmpeg->videoFromPNGs($this->screenshotsDir, "$this->screenshotsDir/video") && file_exists("$this->screenshotsDir/video.mp4"))
        {
            $this->logDebug('Скриншоты были успешно преобразованы в видео');

            $files = array_filter(scandir($this->screenshotsDir), function ($file) {return strlen($file) === 12 && strpos($file, '.png', -4) !== false;});

            array_pop($files);

            $this->logDebug('Удаляем все скриншоты кроме последнего');

            foreach ($files as $file)
            {
                unlink("$this->screenshotsDir/$file");
            }
        }

        TestProperties::save($this->logDir . '/test_properties.json');

        echo PHP_EOL . 'Ссылка на HTML-лог прогона: ' . PHP_EOL . "file://$this->logFilePath" . PHP_EOL;

        $this->_setOutputFile();
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function logEmergency($message, array $context = array())
    {
        Log::get()->addEmergency($message, $context);
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
    public function logAlert($message, array $context = array())
    {
        Log::get()->addAlert($message, $context);
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
    public function logCritical($message, array $context = array())
    {
        Log::get()->addCritical($message, $context);
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
    public function logError($message, array $context = array())
    {
        Log::get()->addError($message, $context);
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
    public function logWarning($message, array $context = array())
    {
        Log::get()->addWarning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function logNotice($message, array $context = array())
    {
        Log::get()->addNotice($message, $context);
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
    public function logInfo($message, array $context = array())
    {
        Log::get()->addInfo($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function logDebug($message, array $context = array())
    {
        Log::get()->addDebug($message, $context);
    }
}
