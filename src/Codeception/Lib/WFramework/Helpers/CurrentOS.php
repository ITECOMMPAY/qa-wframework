<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 18.04.19
 * Time: 10:59
 */

namespace Codeception\Lib\WFramework\Helpers;


use function strtolower;
use function substr;

class CurrentOS
{
    const WINDOWS = 'WINDOWS';
    const MAC = 'MAC';
    const LINUX = 'LINUX';
    const UNKNOWN = 'UNKNOWN';

    protected static $os = null;

    public static function get() : string
    {
        if (static::$os !== null)
        {
            return static::$os;
        }

        $os = strtolower(substr(php_uname(), 0, 3));

        switch ($os)
        {
            case 'win':
                static::$os = static::WINDOWS;
                break;

            case 'dar':
            case 'mac':
                static::$os = static::MAC;
                break;

            case 'lin':
                static::$os = static::LINUX;
                break;

            default:
                static::$os = static::UNKNOWN;
        }

        return static::$os;
    }
}
