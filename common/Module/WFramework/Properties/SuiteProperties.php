<?php


namespace Common\Module\WFramework\Properties;


use function is_array;
use function is_object;

/**
 * Класс для централизованного хранения тестовых данных.
 *
 * Перед каждым цестом - очищается и заполняется значениями из GlobalProperties.
 * Перед каждым тестом TestProperties очищается и заполняется значениями из SuiteProperties.
 *
 * Брать данные в тестах следует из TestProperties.
 */
class SuiteProperties
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

        TestProperties::setValue($option, $value);
    }

    public static function setValues(array $options)
    {
        foreach ($options as $key => $value)
        {
            if (is_array($value) || is_object($value)) continue;

            static::setValue($key, (string) $value);
        }
    }

    public static function unsetValue(string $option)
    {
        if (isset(static::$properties[$option]))
        {
            unset(static::$properties[$option]);
        }
    }

    public static function unsetValues(array $options)
    {
        foreach ($options as $option)
        {
            static::unsetValue($option);
        }
    }

    public static function getProperties() : array
    {
        return static::$properties;
    }

    public static function clear()
    {
        static::$properties = GlobalProperties::getProperties();
    }
}
