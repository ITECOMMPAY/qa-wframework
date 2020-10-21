<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.02.19
 * Time: 17:58
 */

namespace Common\Module\WFramework\WLocator;

/**
 * Класс описывающий пустой локатор Селениума.
 *
 * Используется, чтобы минимизировать использование null'ов во фреймворке, там где это целесообразно.
 *
 * Является синглтоном.
 * @package Common\Module\WFramework\WLocator
 */
class EmptyLocator extends WLocator
{
    // Здесь начинается стандартный код синглтона

    private static $instances = array();

    private function __clone(){}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton!');
    }

    public static function get() : EmptyLocator
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
        parent::__construct('xpath', '.');
    }

    // Здесь кончается стандартный код синглтона

    public function isEmpty() : bool
    {
        return true;
    }

    public function isHtmlRoot() : bool
    {
        return false;
    }
}
