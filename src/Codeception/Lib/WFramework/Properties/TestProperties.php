<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 03.04.19
 * Time: 15:10
 */

namespace Codeception\Lib\WFramework\Properties;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use function is_array;
use function is_object;

/**
 * Класс для централизованного хранения тестовых данных.
 *
 * Перед каждым тестом в цесте - очищается и заполняется значениями из SuiteProperties, которые заполняются из GlobalProperties
 */
class TestProperties
{
    protected static $properties = [];

    /**
     * Сохраняет строковое значение $value в параметр $option
     * @param string $option
     * @param string $value
     */
    public static function setValue(string $option, string $value)
    {
        static::$properties[$option] = $value;
    }

    public static function setValues(array $options)
    {
        foreach ($options as $key => $value)
        {
            if (is_array($value) || is_object($value)) continue;

            static::setValue($key, (string) $value);
        }
    }

    /**
     * Возвращает строковое значение параметра $option.
     *
     * Если параметр $option не задан в TestProperties - возвращает значение $default.
     *
     * @param string $option
     * @param null $default
     * @return string|null
     */
    public static function getValue(string $option, $default = null)
    {
        $result = $default;

        if (isset(static::$properties[$option]))
        {
            $result = static::$properties[$option];
        }

        return $result;
    }

    /**
     * Возвращает строковое значение параметра $option.
     *
     * Если параметр $option не задан в TestProperties - валит тест.
     *
     * @param string $option
     * @return mixed
     * @throws UsageException
     */
    public static function mustGetValue(string $option)
    {
        if (!isset(static::$properties[$option]))
        {
            throw new UsageException("В TestProperties не задано свойство: $option");
        }

        return static::$properties[$option];
    }

    public static function haveValue(string $option) : bool
    {
        return isset(static::$properties[$option]);
    }

    public static function clear()
    {
        static::$properties = SuiteProperties::getProperties();
    }

    public static function save(string $filename)
    {
        $data = json_encode(static::$properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents($filename, $data);
    }

    public static function load(string $filename)
    {
        $data = file_get_contents($filename);

        $decodedData = json_decode($data, true);

        if ($decodedData === null)
        {
            throw new UsageException("$filename - не является валидным JSON!");
        }

        static::$properties = $decodedData;
    }

    /**
     * Создаёт копию значения $fromOption под новым именем $toOption
     *
     * @param string $fromOption
     * @param string $toOption
     * @throws UsageException
     */
    public static function copyValue(string $fromOption, string $toOption)
    {
        $value = static::mustGetValue($fromOption);

        static::setValue($toOption, $value);
    }
}
