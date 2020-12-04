<?php


namespace Codeception\Lib\WFramework\WLocator;


use function get_called_class;

class HtmlRoot extends WLocator
{
    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton!');
    }

    public static function get() : HtmlRoot
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
        parent::__construct('xpath', '/html');
    }

    // Здесь кончается стандартный код синглтона

    public function isEmpty() : bool
    {
        return true;
    }

    public function isHtmlRoot() : bool
    {
        return true;
    }
}
