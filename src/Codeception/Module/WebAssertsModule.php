<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 10.04.19
 * Time: 15:54
 */

namespace Codeception\Module;

use DMS\PHPUnitExtensions\ArraySubset\Assert as ArraySubsetAssert;
use Codeception\TestInterface;
use Codeception\Lib\WFramework\Logger\WLogger;
use function get_class;
use function implode;
use function is_object;
use function json_encode;
use PHPUnit\Framework\AssertionFailedError;

/**
 * Данный модуль оборачивает модуль Asserts из Codeception.
 *
 * Его следует использовать вместо оригинала, чтобы облегчить доработку фреймворка в будущем.
 *
 * @package Common\Module\WFramework\Modules
 */
class WebAssertsModule extends Asserts
{
    public function assertEquals($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertEquals - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertEquals($expected, $actual, $message);
    }

    public function assertNotEquals($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotEquals - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertNotEquals($expected, $actual, $message);
    }

    public function assertSame($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertSame - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertSame($expected, $actual, $message);
    }

    public function assertNotSame($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotSame - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertNotSame($expected, $actual, $message);
    }

    public function assertGreaterThan($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertGreaterThan - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertGreaterThan($expected, $actual, $message);
    }

    public function assertGreaterThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertGreaterThanOrEqual - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertGreaterThanOrEqual($expected, $actual, $message);
    }

    public function assertLessThan($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertLessThan - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertLessThan($expected, $actual, $message);
    }

    public function assertLessThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertLessThanOrEqual - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertLessThanOrEqual($expected, $actual, $message);
    }

    public function assertContains($needle, $haystack, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertContains - $message " . PHP_EOL . "[что: " . json_encode($needle) . " | где: " . json_encode($haystack) . "]", $context);

        parent::assertContains($needle, $haystack, $message);
    }

    public function assertNotContains($needle, $haystack, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotContains - $message " . PHP_EOL . "[что: " . json_encode($needle) . " | где: " . json_encode($haystack) . "]", $context);

        parent::assertNotContains($needle, $haystack, $message);
    }

    public function assertRegExp($pattern, $string, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertRegExp - $message " . PHP_EOL . "[паттерн: $pattern | строка: $string]", $context);

        parent::assertRegExp($pattern, $string, $message);
    }

    public function assertNotRegExp($pattern, $string, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotRegExp - $message " . PHP_EOL . "[паттерн: $pattern | строка: $string]", $context);

        parent::assertNotRegExp($pattern, $string, $message);
    }

    public function assertStringStartsWith($prefix, $string, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertStringStartsWith - $message " . PHP_EOL . "[префикс: $prefix | строка: $string]", $context);

        parent::assertStringStartsWith($prefix, $string, $message);
    }

    public function assertStringStartsNotWith($prefix, $string, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertStringStartsNotWith - $message " . PHP_EOL . "[префикс: $prefix | строка: $string]", $context);

        parent::assertStringStartsNotWith($prefix, $string, $message);
    }

    public function assertEmpty($actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertEmpty - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        parent::assertEmpty($actual, $message);
    }

    public function assertNotEmpty($actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotEmpty - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        parent::assertNotEmpty($actual, $message);
    }

    public function assertNull($actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNull - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        parent::assertNull($actual, $message);
    }

    public function assertNotNull($actual, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotNull - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        parent::assertNotNull($actual, $message);
    }

    public function assertTrue($condition, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertTrue - $message " . PHP_EOL . "[актуальное: " . json_encode($condition) . "]", $context);

        parent::assertTrue($condition, $message);
    }

    public function assertFalse($condition, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertFalse - $message " . PHP_EOL . "[актуальное: " . json_encode($condition) . "]", $context);

        parent::assertFalse($condition, $message);
    }

    public function assertFileExists($filename, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertFileExists - $message " . PHP_EOL . "[имя файла: $filename]", $context);

        parent::assertFileExists($filename, $message);
    }

    public function assertFileNotExists($filename, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertFileNotExists - $message " . PHP_EOL . "[имя файла: $filename]", $context);

        parent::assertFileNotExists($filename, $message);
    }

    public function assertGreaterOrEquals($expected, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertGreaterOrEquals - $description " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertGreaterOrEquals($expected, $actual, $description);
    }

    public function assertLessOrEquals($expected, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertLessOrEquals - $description " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        parent::assertLessOrEquals($expected, $actual, $description);
    }

    public function assertIsEmpty($actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertIsEmpty - $description " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        parent::assertIsEmpty($actual, $description);
    }

    public function assertArrayHasKey($key, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertArrayHasKey - $description " . PHP_EOL . "[ключ: $key | массив: " . json_encode($actual) . "]", $context);

        parent::assertArrayHasKey($key, $actual, $description);
    }

    public function assertArrayNotHasKey($key, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertArrayNotHasKey - $description " . PHP_EOL . "[ключ: $key | массив: " . json_encode($actual) . "]", $context);

        parent::assertArrayNotHasKey($key, $actual, $description);
    }

    public function assertCount($expectedCount, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertCount - $description " . PHP_EOL . "[ожидаемое число элементов: $expectedCount | коллекция: " . json_encode($actual) . "]", $context);

        parent::assertCount($expectedCount, $actual, $description);
    }

    public function assertInstanceOf($class, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertInstanceOf - $description " . PHP_EOL . "[класс: $class | объект: " . get_class($actual) . "]", $context);

        parent::assertInstanceOf($class, $actual, $description);
    }

    public function assertNotInstanceOf($class, $actual, $description = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertNotInstanceOf - $description " . PHP_EOL . "[класс: $class | объект: " . get_class($actual) . "]", $context);

        parent::assertNotInstanceOf($class, $actual, $description);
    }

    public function assertArraySubset($subset, $array, $strict = false, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertArraySubset - $message " . PHP_EOL . "[подмассив: " . json_encode($subset) . " | массив: " . json_encode($array) . "]", $context);

        ArraySubsetAssert::assertArraySubset($subset, $array, $strict, $message);
    }

    function assertEqualsWithDelta($expected, $actual, $delta, $message = '', $context = [])
    {
        WLogger::logAlert("Проверка: assertEqualsWithDelta - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . " (погрешность: $delta)]", $context);

        parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
    }

    public function fail($message = '', $context = [])
    {
        WLogger::logAlert("Fail: $message", $context);

        parent::fail($message);
    }

    public function expectException($exception, $callback, $context = [])
    {
        $exceptionClass = $exception;

        if (is_object($exception))
        {
            $exceptionClass = get_class($exception);
        }

        WLogger::logAlert("Проверка: expectException - " . PHP_EOL . "[класс: $exceptionClass]", $context);

        return parent::expectException($exception, $callback);
    }









    protected $failedSoftAssertions = [];

    public function _before(TestInterface $test)
    {
        $this->failedSoftAssertions = [];
    }

    public function assertAll()
    {
        if (empty($this->failedSoftAssertions))
        {
            return;
        }

        throw new AssertionFailedError(implode(PHP_EOL, $this->failedSoftAssertions));
    }

    protected function softAssert(string $functionName, ...$parameters)
    {
        try
        {
            parent::$functionName(...$parameters);
        }
        catch (AssertionFailedError $e)
        {
            $this->failedSoftAssertions[] = $e->getMessage();
            WLogger::logWarning($e->getMessage());
        }
    }

    public function assertSoftEquals($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftEquals - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertEquals', $expected, $actual, $message);
    }

    public function assertSoftNotEquals($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotEquals - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertNotEquals', $expected, $actual, $message);
    }

    public function assertSoftSame($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftSame - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertSame', $expected, $actual, $message);
    }

    public function assertSoftNotSame($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotSame - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertNotSame', $expected, $actual, $message);
    }

    public function assertSoftGreaterThan($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftGreaterThan - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertGreaterThan', $expected, $actual, $message);
    }

    public function assertSoftGreaterThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftGreaterThanOrEqual - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertGreaterThanOrEqual', $expected, $actual, $message);
    }

    public function assertSoftLessThan($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftLessThan - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertLessThan', $expected, $actual, $message);
    }

    public function assertSoftLessThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftLessThanOrEqual - $message " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertLessThanOrEqual', $expected, $actual, $message);
    }

    public function assertSoftContains($needle, $haystack, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftContains - $message " . PHP_EOL . "[что: " . json_encode($needle) . " | где: " . json_encode($haystack) . "]", $context);

        $this->softAssert('assertContains', $needle, $haystack, $message);
    }

    public function assertSoftNotContains($needle, $haystack, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotContains - $message " . PHP_EOL . "[что: " . json_encode($needle) . " | где: " . json_encode($haystack) . "]", $context);

        $this->softAssert('assertNotContains', $needle, $haystack, $message);
    }

    public function assertSoftRegExp($pattern, $string, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftRegExp - $message " . PHP_EOL . "[паттерн: $pattern | строка: $string]", $context);

        $this->softAssert('assertRegExp', $pattern, $string, $message);
    }

    public function assertSoftNotRegExp($pattern, $string, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotRegExp - $message " . PHP_EOL . "[паттерн: $pattern | строка: $string]", $context);

        $this->softAssert('assertNotRegExp', $pattern, $string, $message);
    }

    public function assertSoftStringStartsWith($prefix, $string, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftStringStartsWith - $message " . PHP_EOL . "[префикс: $prefix | строка: $string]", $context);

        $this->softAssert('assertStringStartsWith', $prefix, $string, $message);
    }

    public function assertSoftStringStartsNotWith($prefix, $string, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftStringStartsNotWith - $message " . PHP_EOL . "[префикс: $prefix | строка: $string]", $context);

        $this->softAssert('assertStringStartsNotWith', $prefix, $string, $message);
    }

    public function assertSoftEmpty($actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftEmpty - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertEmpty', $actual, $message);
    }

    public function assertSoftNotEmpty($actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotEmpty - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertNotEmpty', $actual, $message);
    }

    public function assertSoftNull($actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNull - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertNull', $actual, $message);
    }

    public function assertSoftNotNull($actual, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotNull - $message " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertNotNull', $actual, $message);
    }

    public function assertSoftTrue($condition, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftTrue - $message " . PHP_EOL . "[актуальное: " . json_encode($condition) . "]", $context);

        $this->softAssert('assertTrue', $condition, $message);
    }

    public function assertSoftFalse($condition, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftFalse - $message " . PHP_EOL . "[актуальное: " . json_encode($condition) . "]", $context);

        $this->softAssert('assertFalse', $condition, $message);
    }

    public function assertSoftFileExists($filename, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftFileExists - $message " . PHP_EOL . "[имя файла: $filename]", $context);

        $this->softAssert('assertFileExists', $filename, $message);
    }

    public function assertSoftFileNotExists($filename, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftFileNotExists - $message " . PHP_EOL . "[имя файла: $filename]", $context);

        $this->softAssert('assertFileNotExists', $filename, $message);
    }

    public function assertSoftGreaterOrEquals($expected, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftGreaterOrEquals - $description " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertGreaterOrEquals', $expected, $actual, $description);
    }

    public function assertSoftLessOrEquals($expected, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftLessOrEquals - $description " . PHP_EOL . "[ожидаемое: " . json_encode($expected) . " | актуальное: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertLessOrEquals', $expected, $actual, $description);
    }

    public function assertSoftIsEmpty($actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftIsEmpty - $description " . PHP_EOL . "[значение: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertIsEmpty', $actual, $description);
    }

    public function assertSoftArrayHasKey($key, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftArrayHasKey - $description " . PHP_EOL . "[ключ: $key | массив: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertArrayHasKey', $key, $actual, $description);
    }

    public function assertSoftArrayNotHasKey($key, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftArrayNotHasKey - $description " . PHP_EOL . "[ключ: $key | массив: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertArrayNotHasKey', $key, $actual, $description);
    }

    public function assertSoftArraySubset($subset, $array, $strict = false, $message = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftArraySubset - $message " . PHP_EOL . "[подмассив: " . json_encode($subset) . " | массив: " . json_encode($array) . "]", $context);

        $this->softAssert('assertArraySubset', $subset, $array, $strict, $message);
    }

    public function assertSoftCount($expectedCount, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftCount - $description " . PHP_EOL . "[ожидаемое число элементов: $expectedCount | коллекция: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertCount', $expectedCount, $actual, $description);
    }

    public function assertSoftInstanceOf($class, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftInstanceOf - $description " . PHP_EOL . "[класс: $class | объект: " . get_class($actual) . "]", $context);

        $this->softAssert('assertInstanceOf', $class, $actual, $description);
    }

    public function assertSoftNotInstanceOf($class, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftNotInstanceOf - $description " . PHP_EOL . "[класс: $class | объект: " . get_class($actual) . "]", $context);

        $this->softAssert('assertNotInstanceOf', $class, $actual, $description);
    }

    public function assertSoftInternalType($type, $actual, $description = '', $context = [])
    {
        WLogger::logCritical("Проверка: assertSoftInternalType - $description " . PHP_EOL . "[тип: $type | объект: " . json_encode($actual) . "]", $context);

        $this->softAssert('assertInternalType', $type, $actual, $description);
    }

    public function failSoft($message, $context = [])
    {
        WLogger::logCritical("Soft Fail: $message", $context);

        $this->softAssert('fail', $message, $context);
    }

    public function expectExceptionSoft($exception, $callback, $context = [])
    {
        $exceptionClass = $exception;

        if (is_object($exception))
        {
            $exceptionClass = get_class($exception);
        }

        WLogger::logCritical("Проверка: expectExceptionSoft - " . PHP_EOL . "[класс: $exceptionClass]", $context);

        $this->softAssert('expectException', $exception, $callback);
    }
}
