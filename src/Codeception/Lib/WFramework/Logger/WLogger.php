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
    /** @var ILoggerModule */
    protected static $loggerModule;

    public static function setLoggerModule(ILoggerModule $module)
    {
        static::$loggerModule = $module;
    }

    protected static function log(string $function, $object, $message, array $context = [])
    {
        if (static::$loggerModule !== null)
        {
            static::$loggerModule->$function($object, $message, $context);
            return;
        }

        if ($function === 'logInfo')
        {
            echo  '        ' . $message . PHP_EOL;
            return;
        }

        if ($function === 'logDebug')
        {
            echo '                ' . $message . PHP_EOL;
            return;
        }

        echo $message . PHP_EOL;
    }

    public static function logAction($object, string $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }

    public static function logNotice($object, $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }

    public static function logInfo($object, $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }

    public static function logDebug($object, $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }





    public static function logError($object, string $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }

    public static function logWarning($object, string $message, array $context = [])
    {
        static::log(__FUNCTION__, $object, $message, $context);
    }





    public static function logAssertHard(string $message, array $context = [])
    {
        if (static::$loggerModule !== null)
        {
            static::$loggerModule->logAssertHard($message, $context);
            return;
        }

        echo $message . PHP_EOL;
    }

    public static function logAssertSoft(string $message, array $context = [])
    {
        if (static::$loggerModule !== null)
        {
            static::$loggerModule->logAssertSoft($message, $context);
            return;
        }

        echo $message . PHP_EOL;
    }
}
