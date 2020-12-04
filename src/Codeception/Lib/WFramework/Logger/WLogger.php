<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 27.03.19
 * Time: 16:50
 */

namespace Codeception\Lib\WFramework\Logger;

/**
 * Данный класс служит для обращения к логу из кода фреймворка.
 *
 * Для обращения к логу из тестов Codeception следует использовать LoggerModule.
 *
 * @package Common\Module\WFramework\Logger
 */
class WLogger
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public static function logEmergency($message, array $context = array())
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
    public static function logAlert($message, array $context = array())
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
    public static function logCritical($message, array $context = array())
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
    public static function logError($message, array $context = array())
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
    public static function logWarning($message, array $context = array())
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
    public static function logNotice($message, array $context = array())
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
    public static function logInfo($message, array $context = array())
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
    public static function logDebug($message, array $context = array())
    {
        Log::get()->addDebug($message, $context);
    }
}
