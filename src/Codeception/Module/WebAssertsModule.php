<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 10.04.19
 * Time: 15:54
 */

namespace Codeception\Module;


use Codeception\TestInterface;
use Codeception\Lib\WFramework\Logger\WLogger;
use Ds\Map;
use ReflectionMethod;
use function implode;
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
    protected function getParametersMap(string $functionName, array &$parameters) : Map
    {
        $ref = new ReflectionMethod($this, $functionName);

        $result = new Map();

        foreach ($ref->getParameters() as $key => $param)
        {
            if (isset($parameters[$key]))
            {
                $result[$param->getName()] = $parameters[$key];
                continue;
            }

            $result[$param->getName()] = $param->getDefaultValue();
        }

        return $result;
    }

    protected function printParametersMap(Map $parametersMap) : string
    {
        if ($parametersMap->hasKey('context'))
        {
            $parametersMap->remove('context');
        }

        if ($parametersMap->hasKey('message'))
        {
            $parametersMap->remove('message');
        }

        $paramToValueStrings = [];

        foreach ($parametersMap as $key => $value)
        {
            $paramToValueStrings[] = $key . ': ' . json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

        return implode(' | ', $paramToValueStrings);
    }

    protected function log(string $functionName, array $parameters, bool $hard = true)
    {
        $parametersMap = $this->getParametersMap($functionName, $parameters);

        $context = $parametersMap->get('context', []);
        $message = $parametersMap->get('message', '');
        $parametersString = $this->printParametersMap($parametersMap);

        $logMessage = "$functionName: $message";

        if (!empty($parametersString))
        {
            $logMessage .= PHP_EOL . '    ' . '[' . $parametersString . ']';
        }

        if ($hard)
        {
            WLogger::logAssertHard($logMessage, $context);
        }
        else
        {
            WLogger::logAssertSoft($logMessage, $context);
        }
    }

    protected function hardAssert(string $functionName, array $parameters)
    {
        $this->log($functionName, $parameters, true);

        parent::$functionName(...$parameters);
    }

    protected function softAssert(string $functionName, ...$parameters)
    {
        $this->log($functionName, $parameters, false);

        try
        {
            parent::$functionName(...$parameters);
        }
        catch (AssertionFailedError $e)
        {
            $this->failedSoftAssertions[] = $e->getMessage();
            WLogger::logWarning($this, $e->getMessage());
        }
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


















    public function assertFileNotExists($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertGreaterOrEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsEmpty($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertLessOrEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotRegExp($pattern, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertRegExp($pattern, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertThatItsNot($value, $constraint, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function expectThrowable($throwable, $callback, $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertArrayHasKey($key, $array, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertArrayNotHasKey($key, $array, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertClassHasAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertClassHasStaticAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertClassNotHasAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertClassNotHasStaticAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertContains($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertContainsEquals($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertContainsOnly($type, $haystack, $isNativeType = null, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertContainsOnlyInstancesOf($className, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertCount($expectedCount, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryDoesNotExist($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryExists($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryIsNotReadable($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryIsNotWritable($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryIsReadable($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDirectoryIsWritable($directory, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertDoesNotMatchRegularExpression($pattern, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertEmpty($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertEqualsWithDelta($expected, $actual, $delta, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFalse($condition, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileDoesNotExist($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileExists($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileIsNotReadable($file, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileIsNotWritable($file, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileIsReadable($file, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileIsWritable($file, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileNotEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileNotEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFileNotEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertFinite($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertGreaterThan($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertGreaterThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertInfinite($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertInstanceOf($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsArray($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsBool($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsCallable($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsClosedResource($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsFloat($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsInt($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsIterable($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotArray($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotBool($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotCallable($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotClosedResource($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotFloat($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotInt($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotIterable($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotNumeric($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotObject($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotReadable($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotResource($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotScalar($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotString($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNotWritable($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsNumeric($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsObject($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsReadable($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsResource($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsScalar($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsString($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertIsWritable($filename, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJson($actualJson, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertLessThan($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertLessThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertMatchesRegularExpression($pattern, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNan($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotContains($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotContainsEquals($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotContainsOnly($type, $haystack, $isNativeType = null, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotCount($expectedCount, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotEmpty($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotEquals($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotEqualsWithDelta($expected, $actual, $delta, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotFalse($condition, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotInstanceOf($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotNull($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotSame($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotSameSize($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNotTrue($condition, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertNull($actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertObjectHasAttribute($attributeName, $object, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertObjectNotHasAttribute($attributeName, $object, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertSame($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertSameSize($expected, $actual, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringContainsString($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringContainsStringIgnoringCase($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringEndsNotWith($suffix, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringEndsWith($suffix, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringEqualsFile($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringEqualsFileCanonicalizing($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringEqualsFileIgnoringCase($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringMatchesFormat($format, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringMatchesFormatFile($formatFile, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotContainsString($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotContainsStringIgnoringCase($needle, $haystack, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotEqualsFile($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotEqualsFileCanonicalizing($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotEqualsFileIgnoringCase($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotMatchesFormat($format, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringNotMatchesFormatFile($formatFile, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringStartsNotWith($prefix, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertStringStartsWith($prefix, $string, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertTrue($condition, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }

    public function fail($message = '', $context = [])
    {
        $this->hardAssert(__FUNCTION__, func_get_args());
    }


















    public function assertSoftFileNotExists($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftGreaterOrEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsEmpty($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftLessOrEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotRegExp($pattern, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftRegExp($pattern, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftThatItsNot($value, $constraint, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function expectThrowableSoft($throwable, $callback, $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftArrayHasKey($key, $array, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftArrayNotHasKey($key, $array, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftClassHasAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftClassHasStaticAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftClassNotHasAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftClassNotHasStaticAttribute($attributeName, $className, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftContains($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftContainsEquals($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftContainsOnly($type, $haystack, $isNativeType = null, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftContainsOnlyInstancesOf($className, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftCount($expectedCount, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryDoesNotExist($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryExists($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryIsNotReadable($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryIsNotWritable($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryIsReadable($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDirectoryIsWritable($directory, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftDoesNotMatchRegularExpression($pattern, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftEmpty($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftEqualsWithDelta($expected, $actual, $delta, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFalse($condition, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileDoesNotExist($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileExists($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileIsNotReadable($file, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileIsNotWritable($file, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileIsReadable($file, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileIsWritable($file, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileNotEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileNotEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFileNotEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftFinite($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftGreaterThan($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftGreaterThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftInfinite($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftInstanceOf($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsArray($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsBool($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsCallable($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsClosedResource($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsFloat($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsInt($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsIterable($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotArray($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotBool($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotCallable($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotClosedResource($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotFloat($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotInt($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotIterable($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotNumeric($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotObject($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotReadable($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotResource($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotScalar($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotString($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNotWritable($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsNumeric($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsObject($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsReadable($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsResource($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsScalar($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsString($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftIsWritable($filename, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJson($actualJson, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftLessThan($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftLessThanOrEqual($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftMatchesRegularExpression($pattern, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNan($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotContains($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotContainsEquals($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotContainsOnly($type, $haystack, $isNativeType = null, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotCount($expectedCount, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotEmpty($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotEquals($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotEqualsCanonicalizing($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotEqualsIgnoringCase($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotEqualsWithDelta($expected, $actual, $delta, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotFalse($condition, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotInstanceOf($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotNull($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotSame($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotSameSize($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNotTrue($condition, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftNull($actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftObjectHasAttribute($attributeName, $object, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftObjectNotHasAttribute($attributeName, $object, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftSame($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftSameSize($expected, $actual, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringContainsString($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringContainsStringIgnoringCase($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringEndsNotWith($suffix, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringEndsWith($suffix, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringEqualsFile($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringEqualsFileCanonicalizing($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringEqualsFileIgnoringCase($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringMatchesFormat($format, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringMatchesFormatFile($formatFile, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotContainsString($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotContainsStringIgnoringCase($needle, $haystack, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotEqualsFile($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotEqualsFileCanonicalizing($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotEqualsFileIgnoringCase($expectedFile, $actualString, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotMatchesFormat($format, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringNotMatchesFormatFile($formatFile, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringStartsNotWith($prefix, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftStringStartsWith($prefix, $string, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftTrue($condition, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function assertSoftXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }

    public function failSoft($message = '', $context = [])
    {
        $this->softAssert(__FUNCTION__, func_get_args());
    }
}
