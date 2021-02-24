<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\GeneralException;

class System
{
    public const WINDOWS = 'windows';
    public const MAC = 'mac';
    public const LINUX = 'linux';
    public const UNKNOWN = 'unknown';

    protected static $os = null;

    public static function getOS() : string
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

    public static function getHomeDir() :?string
    {
        // getenv('HOME') isn't set on Windows and generates a Notice.
        $home = getenv('HOME');

        if (!empty($home))
        {
            // home should never end with a trailing slash.
            return rtrim($home, '/');
        }

        if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH']))
        {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            // If HOMEPATH is a root directory the path can end with a slash. Make sure
            // that doesn't happen.
            $home = rtrim($home, '\\/');
        }

        return empty($home) ? null : $home;
    }

    public static function mkDirInHome(string $dir) : string
    {
        $homeDir = static::getHomeDir();

        if ($homeDir === null)
        {
            throw new GeneralException("Can't find the home directory");
        }

        $outputDir = $homeDir . '/' . $dir;

        if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir))
        {
            throw new GeneralException("Can't create the directory: $outputDir");
        }

        return realpath($outputDir);
    }
}