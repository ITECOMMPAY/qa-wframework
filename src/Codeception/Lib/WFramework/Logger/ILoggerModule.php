<?php


namespace Codeception\Lib\WFramework\Logger;


interface ILoggerModule
{
    /**
     * Логирует жёсткий ассерт
     *
     * @param string $message
     * @param array $context
     */
    public function logAssertHard(string $message, array $context = []);

    /**
     * Логирует мягкий ассерт
     *
     * @param string $message
     * @param array $context
     */
    public function logAssertSoft(string $message, array $context = []);

    /**
     * Логирует ошибку
     *
     * @param $object
     * @param string $message
     * @param array $context
     */
    public function logError($object, string $message, array $context = []);

    /**
     * Логирует предупреждение
     *
     * @param $object
     * @param string $message
     * @param array $context
     */
    public function logWarning($object, string $message, array $context = []);

    /**
     * Этот метод должен использоваться в начале тестовых шагов
     *
     * @param $object
     * @param $message
     * @param array $context
     */
    public function logNotice($object, string $message, array $context = []);

    /**
     * Этот метод должен использоваться в начале методов PageObject'ов
     *
     * @param $object
     * @param $message
     * @param array $context
     */
    public function logInfo($object, string $message, array $context = []);

    /**
     * Этот метод можно использовать повсюду для логирования дебажной инфы
     *
     * @param $object
     * @param $message
     * @param array $context
     */
    public function logDebug($object, string $message, array $context = []);

    /**
     * Логирует начало действия (начало шага теста, начало метода PageObject'а)
     *
     * @param $object
     * @param $message
     * @param array $context
     */
    public function logAction($object, string $message, array $context = []);
}