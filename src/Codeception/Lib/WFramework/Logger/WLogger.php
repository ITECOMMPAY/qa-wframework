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
    public static function logNotice($object, $message, array $context = [])
    {
        Log::get()->addNotice($object, $message, $context);
    }

    public static function logInfo($object, $message, array $context = [])
    {
        Log::get()->addInfo($object, $message, $context);
    }

    public static function logDebug($object, $message, array $context = [])
    {
        Log::get()->addDebug($object, $message, $context);
    }



    public static function logError($object, string $message, array $context = [])
    {
        Log::get()->addError($object, $message, $context);
    }

    public static function logWarning($object, string $message, array $context = [])
    {
        Log::get()->addWarning($object, $message, $context);
    }



    public static function logAssertSoft(string $message, array $context = [])
    {
        Log::get()->addAssertSoft($message, $context);
    }

    public static function logAssertHard(string $message, array $context = [])
    {
        Log::get()->addAssertHard($message, $context);
    }



    public static function logAction($object, string $message, array $context = [])
    {
        Log::get()->addSmart($object, $message, $context);
    }
}
