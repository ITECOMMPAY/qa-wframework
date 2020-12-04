<?php


namespace Codeception\Lib\WFramework\Helpers;

use Codeception\Lib\WFramework\Exceptions\Common\UsageException;

class EmptyDateTime extends \DateTimeImmutable
{
    public function diff($datetime2, $absolute = false)
    {
        throw new UsageException('Нельзя вызвать diff() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    public function format($format)
    {
        throw new UsageException('Нельзя вызвать format() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    public function getOffset()
    {
        throw new UsageException('Нельзя вызвать getOffset() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    public function getTimestamp()
    {
        throw new UsageException('Нельзя вызвать getTimestamp() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    public function getTimezone()
    {
        throw new UsageException('Нельзя вызвать getTimezone() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    public function __wakeup()
    {
        throw new UsageException('Нельзя вызвать __wakeup() у пустого DateTimeImmutable - нужно добавить проверку на EmptyDateTime');
    }

    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public static function get() : EmptyDateTime
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    protected function __construct(){
        parent::__construct();
    }

    // Здесь кончается стандартный код синглтона
}
