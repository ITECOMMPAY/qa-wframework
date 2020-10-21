<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 18:49
 */

namespace Common\Module\WFramework\Actor;


use Codeception\Actor;
use Codeception\Exception\ModuleException;
use Codeception\TestInterface;
use PHPUnit\Framework\AssertionFailedError;

abstract class ImaginaryActor extends Actor
{
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are equal. If you're comparing floating-point values,
     * you can specify the optional "delta" parameter which dictates how great of a precision
     * error are you willing to tolerate in order to consider the two values equal.
     *
     * Regular example:
     * ```php
     * <?php
     * $I->assertEquals($element->getChildrenCount(), 5);
     * ```
     *
     * Floating-point example:
     * ```php
     * <?php
     * $I->assertEquals($calculator->add(0.1, 0.2), 0.3, 'Calculator should add the two numbers correctly.', 0.01);
     * ```
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertEquals()
     */
    abstract public function assertEquals($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are not equal. If you're comparing floating-point values,
     * you can specify the optional "delta" parameter which dictates how great of a precision
     * error are you willing to tolerate in order to consider the two values not equal.
     *
     * Regular example:
     * ```php
     * <?php
     * $I->assertNotEquals($element->getChildrenCount(), 0);
     * ```
     *
     * Floating-point example:
     * ```php
     * <?php
     * $I->assertNotEquals($calculator->add(0.1, 0.2), 0.4, 'Calculator should add the two numbers correctly.', 0.01);
     * ```
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @param float  $delta
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotEquals()
     */
    abstract public function assertNotEquals($expected, $actual, $message = null, $delta = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are same
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertSame()
     */
    abstract public function assertSame($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that two variables are not same
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotSame()
     */
    abstract public function assertNotSame($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is greater than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertGreaterThan()
     */
    abstract public function assertGreaterThan($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is greater or equal than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertGreaterThanOrEqual()
     */
    abstract public function assertGreaterThanOrEqual($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is less than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertLessThan()
     */
    abstract public function assertLessThan($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that actual is less or equal than expected
     *
     * @param        $expected
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertLessThanOrEqual()
     */
    abstract public function assertLessThanOrEqual($expected, $actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that haystack contains needle
     *
     * @param        $needle
     * @param        $haystack
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertContains()
     */
    abstract public function assertContains($needle, $haystack, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that haystack doesn't contain needle.
     *
     * @param        $needle
     * @param        $haystack
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotContains()
     */
    abstract public function assertNotContains($needle, $haystack, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that string match with pattern
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertRegExp()
     */
    abstract public function assertRegExp($pattern, $string, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that string not match with pattern
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotRegExp()
     */
    abstract public function assertNotRegExp($pattern, $string, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a string starts with the given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertStringStartsWith()
     */
    abstract public function assertStringStartsWith($prefix, $string, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a string doesn't start with the given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertStringStartsNotWith()
     */
    abstract public function assertStringStartsNotWith($prefix, $string, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is empty.
     *
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertEmpty()
     */
    abstract public function assertEmpty($actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is not empty.
     *
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotEmpty()
     */
    abstract public function assertNotEmpty($actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is NULL
     *
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNull()
     */
    abstract public function assertNull($actual, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that variable is not NULL
     *
     * @param        $actual
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotNull()
     */
    abstract public function assertNotNull($actual, $message = null);

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that condition is positive.
     *
     * @param        $condition
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertTrue()
     */
    abstract public function assertTrue($condition, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that condition is negative.
     *
     * @param        $condition
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertFalse()
     */
    abstract public function assertFalse($condition, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file exists
     *
     * @param string $filename
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertFileExists()
     */
    abstract public function assertFileExists($filename, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file doesn't exist
     *
     * @param string $filename
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertFileNotExists()
     */
    abstract public function assertFileNotExists($filename, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expected
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertGreaterOrEquals()
     */
    abstract public function assertGreaterOrEquals($expected, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expected
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertLessOrEquals()
     */
    abstract public function assertLessOrEquals($expected, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertIsEmpty()
     */
    abstract public function assertIsEmpty($actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $key
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertArrayHasKey()
     */
    abstract public function assertArrayHasKey($key, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $key
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertArrayNotHasKey()
     */
    abstract public function assertArrayNotHasKey($key, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that array contains subset.
     *
     * @param array  $subset
     * @param array  $array
     * @param bool   $strict
     * @param string $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertArraySubset()
     */
    abstract public function assertArraySubset($subset, $array, $strict = null, $message = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $expectedCount
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertCount()
     */
    abstract public function assertCount($expectedCount, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $class
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertInstanceOf()
     */
    abstract public function assertInstanceOf($class, $actual, $description = null);

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $class
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertNotInstanceOf()
     */
    abstract public function assertNotInstanceOf($class, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $type
     * @param $actual
     * @param $description
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::assertInternalType()
     */
    abstract public function assertInternalType($type, $actual, $description = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Fails the test with message.
     *
     * @param $message
     * @throws AssertionFailedError
     * @see \Codeception\Module\Asserts::fail()
     */
    abstract public function fail($message);

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Handles and checks exception called inside callback function.
     * Either exception class name or exception instance should be provided.
     *
     * ```php
     * <?php
     * $I->expectException(MyException::class, function() {
     *     $this->doSomethingBad();
     * });
     *
     * $I->expectException(new MyException(), function() {
     *     $this->doSomethingBad();
     * });
     * ```
     * If you want to check message or exception code, you can pass them with exception instance:
     * ```php
     * <?php
     * // will check that exception MyException is thrown with "Don't do bad things" message
     * $I->expectException(new MyException("Don't do bad things"), function() {
     *     $this->doSomethingBad();
     * });
     * ```
     *
     * @param $exception string or \Exception
     * @param $callback
     * @see \Codeception\Module\Asserts::expectException()
     */
    abstract public function expectException($exception, $callback);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * @see \Common\Module\WFramework\Logger\Log::setOutputFile()
     */
    abstract public function setOutputFile($filename = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logEmergency()
     */
    abstract public function logEmergency($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logAlert()
     */
    abstract public function logAlert($message, $context = null);

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logCritical()
     */
    abstract public function logCritical($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logError()
     */
    abstract public function logError($message, $context = null);

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logWarning()
     */
    abstract public function logWarning($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logNotice()
     */
    abstract public function logNotice($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logInfo()
     */
    abstract public function logInfo($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @see \Common\Module\WFramework\Logger\Log::logDebug()
     */
    abstract public function logDebug($message, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * @see \Common\Module\WFramework\Modules\WebTestingModule::autostartSeleniumServer()
     */
    abstract public function autostartSeleniumServer();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * @see \Common\Module\WFramework\Modules\WebTestingModule::getWebDriver()
     */
    abstract public function getWebDriver();

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Print out latest Selenium Logs in debug mode
     *
     * @param TestInterface $test
     * @see \Codeception\Module\WebDriver::debugWebDriverLogs()
     */
    abstract public function debugWebDriverLogs($test = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Changes the subdomain for the 'url' configuration parameter.
     * Does not open a page; use `amOnPage` for that.
     *
     * ``` php
     * <?php
     * // If config is: 'http://mysite.com'
     * // or config is: 'http://www.mysite.com'
     * // or config is: 'http://company.mysite.com'
     *
     * $I->amOnSubdomain('user');
     * $I->amOnPage('/');
     * // moves to http://user.mysite.com/
     * ?>
     * ```
     *
     * @param $subdomain
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::amOnSubdomain()
     */
    abstract public function amOnSubdomain($subdomain);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Takes a screenshot of the current window and saves it to `tests/_output/debug`.
     *
     * ``` php
     * <?php
     * $I->amOnPage('/user/edit');
     * $I->makeScreenshot('edit_page');
     * // saved to: tests/_output/debug/edit_page.png
     * $I->makeScreenshot();
     * // saved to: tests/_output/debug/2017-05-26_14-24-11_4b3403665fea6.png
     * ```
     *
     * @param $name
     * @see \Codeception\Module\WebDriver::makeScreenshot()
     */
    abstract public function makeScreenshot($name = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Resize the current window.
     *
     * ``` php
     * <?php
     * $I->resizeWindow(800, 600);
     *
     * ```
     *
     * @param int $width
     * @param int $height
     * @see \Codeception\Module\WebDriver::resizeWindow()
     */
    abstract public function resizeWindow($width, $height);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a cookie with the given name is set.
     * You can set additional cookie params like `domain`, `path` as array passed in last argument.
     *
     * ``` php
     * <?php
     * $I->seeCookie('PHPSESSID');
     * ?>
     * ```
     *
     * @param $cookie
     * @param array $params
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeCookie()
     */
    abstract public function canSeeCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a cookie with the given name is set.
     * You can set additional cookie params like `domain`, `path` as array passed in last argument.
     *
     * ``` php
     * <?php
     * $I->seeCookie('PHPSESSID');
     * ?>
     * ```
     *
     * @param $cookie
     * @param array $params
     * @return mixed
     * @see \Codeception\Module\WebDriver::seeCookie()
     */
    abstract public function seeCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there isn't a cookie with the given name.
     * You can set additional cookie params like `domain`, `path` as array passed in last argument.
     *
     * @param $cookie
     *
     * @param array $params
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeCookie()
     */
    abstract public function cantSeeCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there isn't a cookie with the given name.
     * You can set additional cookie params like `domain`, `path` as array passed in last argument.
     *
     * @param $cookie
     *
     * @param array $params
     * @return mixed
     * @see \Codeception\Module\WebDriver::dontSeeCookie()
     */
    abstract public function dontSeeCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Sets a cookie with the given name and value.
     * You can set additional cookie params like `domain`, `path`, `expires`, `secure` in array passed as last argument.
     *
     * ``` php
     * <?php
     * $I->setCookie('PHPSESSID', 'el4ukv0kqbvoirg7nkp4dncpk3');
     * ?>
     * ```
     *
     * @param $name
     * @param $val
     * @param array $params
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::setCookie()
     */
    abstract public function setCookie($cookie, $value, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Unsets cookie with the given name.
     * You can set additional cookie params like `domain`, `path` in array passed as last argument.
     *
     * @param $cookie
     *
     * @param array $params
     * @return mixed
     * @see \Codeception\Module\WebDriver::resetCookie()
     */
    abstract public function resetCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs a cookie value.
     * You can set additional cookie params like `domain`, `path` in array passed as last argument.
     *
     * @param $cookie
     *
     * @param array $params
     * @return mixed
     * @see \Codeception\Module\WebDriver::grabCookie()
     */
    abstract public function grabCookie($cookie, $params = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs current page source code.
     *
     * @throws ModuleException if no page was opened.
     *
     * @return string Current page source code.
     * @see \Codeception\Module\WebDriver::grabPageSource()
     */
    abstract public function grabPageSource();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Open web page at the given absolute URL and sets its hostname as the base host.
     *
     * ``` php
     * <?php
     * $I->amOnUrl('http://codeception.com');
     * $I->amOnPage('/quickstart'); // moves to http://codeception.com/quickstart
     * ?>
     * ```
     * @see \Codeception\Module\WebDriver::amOnUrl()
     */
    abstract public function amOnUrl($url);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Opens the page for the given relative URI.
     *
     * ``` php
     * <?php
     * // opens front page
     * $I->amOnPage('/');
     * // opens /register page
     * $I->amOnPage('/register');
     * ```
     *
     * @param string $page
     * @see \Codeception\Module\WebDriver::amOnPage()
     */
    abstract public function amOnPage($page);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string (case insensitive).
     *
     * You can specify a specific HTML element (via CSS or XPath) as the second
     * parameter to only search within that element.
     *
     * ``` php
     * <?php
     * $I->see('Logout');                        // I can suppose user is logged in
     * $I->see('Sign Up', 'h1');                 // I can suppose it's a signup page
     * $I->see('Sign Up', '//body/h1');          // with XPath
     * $I->see('Sign Up', ['css' => 'body h1']); // with strict CSS locator
     * ```
     *
     * Note that the search is done after stripping all HTML tags from the body,
     * so `$I->see('strong')` will return true for strings like:
     *
     *   - `<p>I am Stronger than thou</p>`
     *   - `<script>document.createElement('strong');</script>`
     *
     * But will *not* be true for strings like:
     *
     *   - `<strong>Home</strong>`
     *   - `<div class="strong">Home</strong>`
     *   - `<!-- strong -->`
     *
     * For checking the raw source code, use `seeInSource()`.
     *
     * @param string $text
     * @param string $selector optional
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::see()
     */
    abstract public function canSee($text, $selector = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string (case insensitive).
     *
     * You can specify a specific HTML element (via CSS or XPath) as the second
     * parameter to only search within that element.
     *
     * ``` php
     * <?php
     * $I->see('Logout');                        // I can suppose user is logged in
     * $I->see('Sign Up', 'h1');                 // I can suppose it's a signup page
     * $I->see('Sign Up', '//body/h1');          // with XPath
     * $I->see('Sign Up', ['css' => 'body h1']); // with strict CSS locator
     * ```
     *
     * Note that the search is done after stripping all HTML tags from the body,
     * so `$I->see('strong')` will return true for strings like:
     *
     *   - `<p>I am Stronger than thou</p>`
     *   - `<script>document.createElement('strong');</script>`
     *
     * But will *not* be true for strings like:
     *
     *   - `<strong>Home</strong>`
     *   - `<div class="strong">Home</strong>`
     *   - `<!-- strong -->`
     *
     * For checking the raw source code, use `seeInSource()`.
     *
     * @param string $text
     * @param string $selector optional
     * @see \Codeception\Module\WebDriver::see()
     */
    abstract public function see($text, $selector = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page doesn't contain the text specified (case insensitive).
     * Give a locator as the second parameter to match a specific region.
     *
     * ```php
     * <?php
     * $I->dontSee('Login');                         // I can suppose user is already logged in
     * $I->dontSee('Sign Up','h1');                  // I can suppose it's not a signup page
     * $I->dontSee('Sign Up','//body/h1');           // with XPath
     * $I->dontSee('Sign Up', ['css' => 'body h1']); // with strict CSS locator
     * ```
     *
     * Note that the search is done after stripping all HTML tags from the body,
     * so `$I->dontSee('strong')` will fail on strings like:
     *
     *   - `<p>I am Stronger than thou</p>`
     *   - `<script>document.createElement('strong');</script>`
     *
     * But will ignore strings like:
     *
     *   - `<strong>Home</strong>`
     *   - `<div class="strong">Home</strong>`
     *   - `<!-- strong -->`
     *
     * For checking the raw source code, use `seeInSource()`.
     *
     * @param string $text
     * @param string $selector optional
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSee()
     */
    abstract public function cantSee($text, $selector = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page doesn't contain the text specified (case insensitive).
     * Give a locator as the second parameter to match a specific region.
     *
     * ```php
     * <?php
     * $I->dontSee('Login');                         // I can suppose user is already logged in
     * $I->dontSee('Sign Up','h1');                  // I can suppose it's not a signup page
     * $I->dontSee('Sign Up','//body/h1');           // with XPath
     * $I->dontSee('Sign Up', ['css' => 'body h1']); // with strict CSS locator
     * ```
     *
     * Note that the search is done after stripping all HTML tags from the body,
     * so `$I->dontSee('strong')` will fail on strings like:
     *
     *   - `<p>I am Stronger than thou</p>`
     *   - `<script>document.createElement('strong');</script>`
     *
     * But will ignore strings like:
     *
     *   - `<strong>Home</strong>`
     *   - `<div class="strong">Home</strong>`
     *   - `<!-- strong -->`
     *
     * For checking the raw source code, use `seeInSource()`.
     *
     * @param string $text
     * @param string $selector optional
     * @see \Codeception\Module\WebDriver::dontSee()
     */
    abstract public function dontSee($text, $selector = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string in its
     * raw source code.
     *
     * ``` php
     * <?php
     * $I->seeInSource('<h1>Green eggs &amp; ham</h1>');
     * ```
     *
     * @param      $raw
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInSource()
     */
    abstract public function canSeeInSource($raw);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string in its
     * raw source code.
     *
     * ``` php
     * <?php
     * $I->seeInSource('<h1>Green eggs &amp; ham</h1>');
     * ```
     *
     * @param      $raw
     * @see \Codeception\Module\WebDriver::seeInSource()
     */
    abstract public function seeInSource($raw);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string in its
     * raw source code.
     *
     * ```php
     * <?php
     * $I->dontSeeInSource('<h1>Green eggs &amp; ham</h1>');
     * ```
     *
     * @param      $raw
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInSource()
     */
    abstract public function cantSeeInSource($raw);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string in its
     * raw source code.
     *
     * ```php
     * <?php
     * $I->dontSeeInSource('<h1>Green eggs &amp; ham</h1>');
     * ```
     *
     * @param      $raw
     * @see \Codeception\Module\WebDriver::dontSeeInSource()
     */
    abstract public function dontSeeInSource($raw);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page source contains the given string.
     *
     * ```php
     * <?php
     * $I->seeInPageSource('<link rel="apple-touch-icon"');
     * ```
     *
     * @param $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInPageSource()
     */
    abstract public function canSeeInPageSource($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page source contains the given string.
     *
     * ```php
     * <?php
     * $I->seeInPageSource('<link rel="apple-touch-icon"');
     * ```
     *
     * @param $text
     * @see \Codeception\Module\WebDriver::seeInPageSource()
     */
    abstract public function seeInPageSource($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page source doesn't contain the given string.
     *
     * @param $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInPageSource()
     */
    abstract public function cantSeeInPageSource($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page source doesn't contain the given string.
     *
     * @param $text
     * @see \Codeception\Module\WebDriver::dontSeeInPageSource()
     */
    abstract public function dontSeeInPageSource($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Perform a click on a link or a button, given by a locator.
     * If a fuzzy locator is given, the page will be searched for a button, link, or image matching the locator string.
     * For buttons, the "value" attribute, "name" attribute, and inner text are searched.
     * For links, the link text is searched.
     * For images, the "alt" attribute and inner text of any parent links are searched.
     *
     * The second parameter is a context (CSS or XPath locator) to narrow the search.
     *
     * Note that if the locator matches a button of type `submit`, the form will be submitted.
     *
     * ``` php
     * <?php
     * // simple link
     * $I->click('Logout');
     * // button of form
     * $I->click('Submit');
     * // CSS button
     * $I->click('#form input[type=submit]');
     * // XPath
     * $I->click('//form/*[@type=submit]');
     * // link in context
     * $I->click('Logout', '#nav');
     * // using strict locator
     * $I->click(['link' => 'Login']);
     * ?>
     * ```
     *
     * @param $link
     * @param $context
     * @see \Codeception\Module\WebDriver::click()
     */
    abstract public function click($link, $context = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there's a link with the specified text.
     * Give a full URL as the second parameter to match links with that exact URL.
     *
     * ``` php
     * <?php
     * $I->seeLink('Logout'); // matches <a href="#">Logout</a>
     * $I->seeLink('Logout','/logout'); // matches <a href="/logout">Logout</a>
     * ?>
     * ```
     *
     * @param string $text
     * @param string $url optional
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeLink()
     */
    abstract public function canSeeLink($text, $url = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there's a link with the specified text.
     * Give a full URL as the second parameter to match links with that exact URL.
     *
     * ``` php
     * <?php
     * $I->seeLink('Logout'); // matches <a href="#">Logout</a>
     * $I->seeLink('Logout','/logout'); // matches <a href="/logout">Logout</a>
     * ?>
     * ```
     *
     * @param string $text
     * @param string $url optional
     * @see \Codeception\Module\WebDriver::seeLink()
     */
    abstract public function seeLink($text, $url = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page doesn't contain a link with the given string.
     * If the second parameter is given, only links with a matching "href" attribute will be checked.
     *
     * ``` php
     * <?php
     * $I->dontSeeLink('Logout'); // I suppose user is not logged in
     * $I->dontSeeLink('Checkout now', '/store/cart.php');
     * ?>
     * ```
     *
     * @param string $text
     * @param string $url optional
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeLink()
     */
    abstract public function cantSeeLink($text, $url = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page doesn't contain a link with the given string.
     * If the second parameter is given, only links with a matching "href" attribute will be checked.
     *
     * ``` php
     * <?php
     * $I->dontSeeLink('Logout'); // I suppose user is not logged in
     * $I->dontSeeLink('Checkout now', '/store/cart.php');
     * ?>
     * ```
     *
     * @param string $text
     * @param string $url optional
     * @see \Codeception\Module\WebDriver::dontSeeLink()
     */
    abstract public function dontSeeLink($text, $url = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current URI contains the given string.
     *
     * ``` php
     * <?php
     * // to match: /home/dashboard
     * $I->seeInCurrentUrl('home');
     * // to match: /users/1
     * $I->seeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInCurrentUrl()
     */
    abstract public function canSeeInCurrentUrl($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current URI contains the given string.
     *
     * ``` php
     * <?php
     * // to match: /home/dashboard
     * $I->seeInCurrentUrl('home');
     * // to match: /users/1
     * $I->seeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::seeInCurrentUrl()
     */
    abstract public function seeInCurrentUrl($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL is equal to the given string.
     * Unlike `seeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeCurrentUrlEquals()
     */
    abstract public function canSeeCurrentUrlEquals($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL is equal to the given string.
     * Unlike `seeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::seeCurrentUrlEquals()
     */
    abstract public function seeCurrentUrlEquals($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL matches the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeCurrentUrlMatches()
     */
    abstract public function canSeeCurrentUrlMatches($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL matches the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::seeCurrentUrlMatches()
     */
    abstract public function seeCurrentUrlMatches($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URI doesn't contain the given string.
     *
     * ``` php
     * <?php
     * $I->dontSeeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInCurrentUrl()
     */
    abstract public function cantSeeInCurrentUrl($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URI doesn't contain the given string.
     *
     * ``` php
     * <?php
     * $I->dontSeeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::dontSeeInCurrentUrl()
     */
    abstract public function dontSeeInCurrentUrl($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL doesn't equal the given string.
     * Unlike `dontSeeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // current url is not root
     * $I->dontSeeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeCurrentUrlEquals()
     */
    abstract public function cantSeeCurrentUrlEquals($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL doesn't equal the given string.
     * Unlike `dontSeeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // current url is not root
     * $I->dontSeeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::dontSeeCurrentUrlEquals()
     */
    abstract public function dontSeeCurrentUrlEquals($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current url doesn't match the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->dontSeeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param string $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeCurrentUrlMatches()
     */
    abstract public function cantSeeCurrentUrlMatches($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current url doesn't match the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->dontSeeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param string $uri
     * @see \Codeception\Module\WebDriver::dontSeeCurrentUrlMatches()
     */
    abstract public function dontSeeCurrentUrlMatches($uri);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Executes the given regular expression against the current URI and returns the first capturing group.
     * If no parameters are provided, the full URI is returned.
     *
     * ``` php
     * <?php
     * $user_id = $I->grabFromCurrentUrl('~$/user/(\d+)/~');
     * $uri = $I->grabFromCurrentUrl();
     * ?>
     * ```
     *
     * @param string $uri optional
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::grabFromCurrentUrl()
     */
    abstract public function grabFromCurrentUrl($uri = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the specified checkbox is checked.
     *
     * ``` php
     * <?php
     * $I->seeCheckboxIsChecked('#agree'); // I suppose user agreed to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user agreed to terms, If there is only one checkbox in form.
     * $I->seeCheckboxIsChecked('//form/input[@type=checkbox and @name=agree]');
     * ?>
     * ```
     *
     * @param $checkbox
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeCheckboxIsChecked()
     */
    abstract public function canSeeCheckboxIsChecked($checkbox);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the specified checkbox is checked.
     *
     * ``` php
     * <?php
     * $I->seeCheckboxIsChecked('#agree'); // I suppose user agreed to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user agreed to terms, If there is only one checkbox in form.
     * $I->seeCheckboxIsChecked('//form/input[@type=checkbox and @name=agree]');
     * ?>
     * ```
     *
     * @param $checkbox
     * @see \Codeception\Module\WebDriver::seeCheckboxIsChecked()
     */
    abstract public function seeCheckboxIsChecked($checkbox);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Check that the specified checkbox is unchecked.
     *
     * ``` php
     * <?php
     * $I->dontSeeCheckboxIsChecked('#agree'); // I suppose user didn't agree to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user didn't check the first checkbox in form.
     * ?>
     * ```
     *
     * @param $checkbox
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeCheckboxIsChecked()
     */
    abstract public function cantSeeCheckboxIsChecked($checkbox);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Check that the specified checkbox is unchecked.
     *
     * ``` php
     * <?php
     * $I->dontSeeCheckboxIsChecked('#agree'); // I suppose user didn't agree to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user didn't check the first checkbox in form.
     * ?>
     * ```
     *
     * @param $checkbox
     * @see \Codeception\Module\WebDriver::dontSeeCheckboxIsChecked()
     */
    abstract public function dontSeeCheckboxIsChecked($checkbox);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given input field or textarea *equals* (i.e. not just contains) the given value.
     * Fields are matched by label text, the "name" attribute, CSS, or XPath.
     *
     * ``` php
     * <?php
     * $I->seeInField('Body','Type your comment here');
     * $I->seeInField('form textarea[name=body]','Type your comment here');
     * $I->seeInField('form input[type=hidden]','hidden_value');
     * $I->seeInField('#searchform input','Search');
     * $I->seeInField('//form/*[@name=search]','Search');
     * $I->seeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInField()
     */
    abstract public function canSeeInField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given input field or textarea *equals* (i.e. not just contains) the given value.
     * Fields are matched by label text, the "name" attribute, CSS, or XPath.
     *
     * ``` php
     * <?php
     * $I->seeInField('Body','Type your comment here');
     * $I->seeInField('form textarea[name=body]','Type your comment here');
     * $I->seeInField('form input[type=hidden]','hidden_value');
     * $I->seeInField('#searchform input','Search');
     * $I->seeInField('//form/*[@name=search]','Search');
     * $I->seeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Module\WebDriver::seeInField()
     */
    abstract public function seeInField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that an input field or textarea doesn't contain the given value.
     * For fuzzy locators, the field is matched by label text, CSS and XPath.
     *
     * ``` php
     * <?php
     * $I->dontSeeInField('Body','Type your comment here');
     * $I->dontSeeInField('form textarea[name=body]','Type your comment here');
     * $I->dontSeeInField('form input[type=hidden]','hidden_value');
     * $I->dontSeeInField('#searchform input','Search');
     * $I->dontSeeInField('//form/*[@name=search]','Search');
     * $I->dontSeeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInField()
     */
    abstract public function cantSeeInField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that an input field or textarea doesn't contain the given value.
     * For fuzzy locators, the field is matched by label text, CSS and XPath.
     *
     * ``` php
     * <?php
     * $I->dontSeeInField('Body','Type your comment here');
     * $I->dontSeeInField('form textarea[name=body]','Type your comment here');
     * $I->dontSeeInField('form input[type=hidden]','hidden_value');
     * $I->dontSeeInField('#searchform input','Search');
     * $I->dontSeeInField('//form/*[@name=search]','Search');
     * $I->dontSeeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Module\WebDriver::dontSeeInField()
     */
    abstract public function dontSeeInField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if the array of form parameters (name => value) are set on the form matched with the
     * passed selector.
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('form[name=myform]', [
     *      'input1' => 'value',
     *      'input2' => 'other value',
     * ]);
     * ?>
     * ```
     *
     * For multi-select elements, or to check values of multiple elements with the same name, an
     * array may be passed:
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('.form-class', [
     *      'multiselect' => [
     *          'value1',
     *          'value2',
     *      ],
     *      'checkbox[]' => [
     *          'a checked value',
     *          'another checked value',
     *      ],
     * ]);
     * ?>
     * ```
     *
     * Additionally, checkbox values can be checked with a boolean.
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('#form-id', [
     *      'checkbox1' => true,        // passes if checked
     *      'checkbox2' => false,       // passes if unchecked
     * ]);
     * ?>
     * ```
     *
     * Pair this with submitForm for quick testing magic.
     *
     * ``` php
     * <?php
     * $form = [
     *      'field1' => 'value',
     *      'field2' => 'another value',
     *      'checkbox1' => true,
     *      // ...
     * ];
     * $I->submitForm('//form[@id=my-form]', $form, 'submitButton');
     * // $I->amOnPage('/path/to/form-page') may be needed
     * $I->seeInFormFields('//form[@id=my-form]', $form);
     * ?>
     * ```
     *
     * @param $formSelector
     * @param $params
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInFormFields()
     */
    abstract public function canSeeInFormFields($formSelector, $params);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if the array of form parameters (name => value) are set on the form matched with the
     * passed selector.
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('form[name=myform]', [
     *      'input1' => 'value',
     *      'input2' => 'other value',
     * ]);
     * ?>
     * ```
     *
     * For multi-select elements, or to check values of multiple elements with the same name, an
     * array may be passed:
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('.form-class', [
     *      'multiselect' => [
     *          'value1',
     *          'value2',
     *      ],
     *      'checkbox[]' => [
     *          'a checked value',
     *          'another checked value',
     *      ],
     * ]);
     * ?>
     * ```
     *
     * Additionally, checkbox values can be checked with a boolean.
     *
     * ``` php
     * <?php
     * $I->seeInFormFields('#form-id', [
     *      'checkbox1' => true,        // passes if checked
     *      'checkbox2' => false,       // passes if unchecked
     * ]);
     * ?>
     * ```
     *
     * Pair this with submitForm for quick testing magic.
     *
     * ``` php
     * <?php
     * $form = [
     *      'field1' => 'value',
     *      'field2' => 'another value',
     *      'checkbox1' => true,
     *      // ...
     * ];
     * $I->submitForm('//form[@id=my-form]', $form, 'submitButton');
     * // $I->amOnPage('/path/to/form-page') may be needed
     * $I->seeInFormFields('//form[@id=my-form]', $form);
     * ?>
     * ```
     *
     * @param $formSelector
     * @param $params
     * @see \Codeception\Module\WebDriver::seeInFormFields()
     */
    abstract public function seeInFormFields($formSelector, $params);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if the array of form parameters (name => value) are not set on the form matched with
     * the passed selector.
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('form[name=myform]', [
     *      'input1' => 'non-existent value',
     *      'input2' => 'other non-existent value',
     * ]);
     * ?>
     * ```
     *
     * To check that an element hasn't been assigned any one of many values, an array can be passed
     * as the value:
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('.form-class', [
     *      'fieldName' => [
     *          'This value shouldn\'t be set',
     *          'And this value shouldn\'t be set',
     *      ],
     * ]);
     * ?>
     * ```
     *
     * Additionally, checkbox values can be checked with a boolean.
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('#form-id', [
     *      'checkbox1' => true,        // fails if checked
     *      'checkbox2' => false,       // fails if unchecked
     * ]);
     * ?>
     * ```
     *
     * @param $formSelector
     * @param $params
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInFormFields()
     */
    abstract public function cantSeeInFormFields($formSelector, $params);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if the array of form parameters (name => value) are not set on the form matched with
     * the passed selector.
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('form[name=myform]', [
     *      'input1' => 'non-existent value',
     *      'input2' => 'other non-existent value',
     * ]);
     * ?>
     * ```
     *
     * To check that an element hasn't been assigned any one of many values, an array can be passed
     * as the value:
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('.form-class', [
     *      'fieldName' => [
     *          'This value shouldn\'t be set',
     *          'And this value shouldn\'t be set',
     *      ],
     * ]);
     * ?>
     * ```
     *
     * Additionally, checkbox values can be checked with a boolean.
     *
     * ``` php
     * <?php
     * $I->dontSeeInFormFields('#form-id', [
     *      'checkbox1' => true,        // fails if checked
     *      'checkbox2' => false,       // fails if unchecked
     * ]);
     * ?>
     * ```
     *
     * @param $formSelector
     * @param $params
     * @see \Codeception\Module\WebDriver::dontSeeInFormFields()
     */
    abstract public function dontSeeInFormFields($formSelector, $params);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Selects an option in a select tag or in radio button group.
     *
     * ``` php
     * <?php
     * $I->selectOption('form select[name=account]', 'Premium');
     * $I->selectOption('form input[name=payment]', 'Monthly');
     * $I->selectOption('//form/select[@name=account]', 'Monthly');
     * ?>
     * ```
     *
     * Provide an array for the second argument to select multiple options:
     *
     * ``` php
     * <?php
     * $I->selectOption('Which OS do you use?', array('Windows','Linux'));
     * ?>
     * ```
     *
     * Or provide an associative array for the second argument to specifically define which selection method should be used:
     *
     * ``` php
     * <?php
     * $I->selectOption('Which OS do you use?', array('text' => 'Windows')); // Only search by text 'Windows'
     * $I->selectOption('Which OS do you use?', array('value' => 'windows')); // Only search by value 'windows'
     * ?>
     * ```
     *
     * @param $select
     * @param $option
     * @see \Codeception\Module\WebDriver::selectOption()
     */
    abstract public function selectOption($select, $option);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Unselect an option in the given select box.
     *
     * @param $select
     * @param $option
     * @see \Codeception\Module\WebDriver::unselectOption()
     */
    abstract public function unselectOption($select, $option);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Ticks a checkbox. For radio buttons, use the `selectOption` method instead.
     *
     * ``` php
     * <?php
     * $I->checkOption('#agree');
     * ?>
     * ```
     *
     * @param $option
     * @see \Codeception\Module\WebDriver::checkOption()
     */
    abstract public function checkOption($option);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Unticks a checkbox.
     *
     * ``` php
     * <?php
     * $I->uncheckOption('#notify');
     * ?>
     * ```
     *
     * @param $option
     * @see \Codeception\Module\WebDriver::uncheckOption()
     */
    abstract public function uncheckOption($option);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Fills a text field or textarea with the given string.
     *
     * ``` php
     * <?php
     * $I->fillField("//input[@type='text']", "Hello World!");
     * $I->fillField(['name' => 'email'], 'jon@mail.com');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Module\WebDriver::fillField()
     */
    abstract public function fillField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Clears given field which isn't empty.
     *
     * ``` php
     * <?php
     * $I->clearField('#username');
     * ?>
     * ```
     *
     * @param $field
     * @see \Codeception\Module\WebDriver::clearField()
     */
    abstract public function clearField($field);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Attaches a file relative to the Codeception `_data` directory to the given file upload field.
     *
     * ``` php
     * <?php
     * // file is stored in 'tests/_data/prices.xls'
     * $I->attachFile('input[@type="file"]', 'prices.xls');
     * ?>
     * ```
     *
     * @param $field
     * @param $filename
     * @see \Codeception\Module\WebDriver::attachFile()
     */
    abstract public function attachFile($field, $filename);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Finds and returns the text contents of the given element.
     * If a fuzzy locator is used, the element is found using CSS, XPath,
     * and by matching the full page source by regular expression.
     *
     * ``` php
     * <?php
     * $heading = $I->grabTextFrom('h1');
     * $heading = $I->grabTextFrom('descendant-or-self::h1');
     * $value = $I->grabTextFrom('~<input value=(.*?)]~sgi'); // match with a regex
     * ?>
     * ```
     *
     * @param $cssOrXPathOrRegex
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::grabTextFrom()
     */
    abstract public function grabTextFrom($cssOrXPathOrRegex);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs the value of the given attribute value from the given element.
     * Fails if element is not found.
     *
     * ``` php
     * <?php
     * $I->grabAttributeFrom('#tooltip', 'title');
     * ?>
     * ```
     *
     *
     * @param $cssOrXpath
     * @param $attribute
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::grabAttributeFrom()
     */
    abstract public function grabAttributeFrom($cssOrXpath, $attribute);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Finds the value for the given form field.
     * If a fuzzy locator is used, the field is found by field name, CSS, and XPath.
     *
     * ``` php
     * <?php
     * $name = $I->grabValueFrom('Name');
     * $name = $I->grabValueFrom('input[name=username]');
     * $name = $I->grabValueFrom('descendant-or-self::form/descendant::input[@name = 'username']');
     * $name = $I->grabValueFrom(['name' => 'username']);
     * ?>
     * ```
     *
     * @param $field
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::grabValueFrom()
     */
    abstract public function grabValueFrom($field);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs either the text content, or attribute values, of nodes
     * matched by $cssOrXpath and returns them as an array.
     *
     * ```html
     * <a href="#first">First</a>
     * <a href="#second">Second</a>
     * <a href="#third">Third</a>
     * ```
     *
     * ```php
     * <?php
     * // would return ['First', 'Second', 'Third']
     * $aLinkText = $I->grabMultiple('a');
     *
     * // would return ['#first', '#second', '#third']
     * $aLinks = $I->grabMultiple('a', 'href');
     * ?>
     * ```
     *
     * @param $cssOrXpath
     * @param $attribute
     * @return string[]
     * @see \Codeception\Module\WebDriver::grabMultiple()
     */
    abstract public function grabMultiple($cssOrXpath, $attribute = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page and is visible.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->seeElement('.error');
     * $I->seeElement('//form/input[1]');
     * $I->seeElement('input', ['name' => 'login']);
     * $I->seeElement('input', ['value' => '123456']);
     *
     * // strict locator in first arg, attributes in second
     * $I->seeElement(['css' => 'form input'], ['name' => 'login']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @return
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeElement()
     */
    abstract public function canSeeElement($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page and is visible.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->seeElement('.error');
     * $I->seeElement('//form/input[1]');
     * $I->seeElement('input', ['name' => 'login']);
     * $I->seeElement('input', ['value' => '123456']);
     *
     * // strict locator in first arg, attributes in second
     * $I->seeElement(['css' => 'form input'], ['name' => 'login']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @return
     * @see \Codeception\Module\WebDriver::seeElement()
     */
    abstract public function seeElement($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element is invisible or not present on the page.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->dontSeeElement('.error');
     * $I->dontSeeElement('//form/input[1]');
     * $I->dontSeeElement('input', ['name' => 'login']);
     * $I->dontSeeElement('input', ['value' => '123456']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeElement()
     */
    abstract public function cantSeeElement($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element is invisible or not present on the page.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->dontSeeElement('.error');
     * $I->dontSeeElement('//form/input[1]');
     * $I->dontSeeElement('input', ['name' => 'login']);
     * $I->dontSeeElement('input', ['value' => '123456']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @see \Codeception\Module\WebDriver::dontSeeElement()
     */
    abstract public function dontSeeElement($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page, even it is invisible.
     *
     * ``` php
     * <?php
     * $I->seeElementInDOM('//form/input[type=hidden]');
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeElementInDOM()
     */
    abstract public function canSeeElementInDOM($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page, even it is invisible.
     *
     * ``` php
     * <?php
     * $I->seeElementInDOM('//form/input[type=hidden]');
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @see \Codeception\Module\WebDriver::seeElementInDOM()
     */
    abstract public function seeElementInDOM($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Opposite of `seeElementInDOM`.
     *
     * @param $selector
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeElementInDOM()
     */
    abstract public function cantSeeElementInDOM($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Opposite of `seeElementInDOM`.
     *
     * @param $selector
     * @param array $attributes
     * @see \Codeception\Module\WebDriver::dontSeeElementInDOM()
     */
    abstract public function dontSeeElementInDOM($selector, $attributes = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there are a certain number of elements matched by the given locator on the page.
     *
     * ``` php
     * <?php
     * $I->seeNumberOfElements('tr', 10);
     * $I->seeNumberOfElements('tr', [0,10]); // between 0 and 10 elements
     * ?>
     * ```
     * @param $selector
     * @param mixed $expected int or int[]
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeNumberOfElements()
     */
    abstract public function canSeeNumberOfElements($selector, $expected);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there are a certain number of elements matched by the given locator on the page.
     *
     * ``` php
     * <?php
     * $I->seeNumberOfElements('tr', 10);
     * $I->seeNumberOfElements('tr', [0,10]); // between 0 and 10 elements
     * ?>
     * ```
     * @param $selector
     * @param mixed $expected int or int[]
     * @see \Codeception\Module\WebDriver::seeNumberOfElements()
     */
    abstract public function seeNumberOfElements($selector, $expected);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeNumberOfElementsInDOM()
     */
    abstract public function canSeeNumberOfElementsInDOM($selector, $expected);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * @see \Codeception\Module\WebDriver::seeNumberOfElementsInDOM()
     */
    abstract public function seeNumberOfElementsInDOM($selector, $expected);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is selected.
     *
     * ``` php
     * <?php
     * $I->seeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeOptionIsSelected()
     */
    abstract public function canSeeOptionIsSelected($selector, $optionText);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is selected.
     *
     * ``` php
     * <?php
     * $I->seeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::seeOptionIsSelected()
     */
    abstract public function seeOptionIsSelected($selector, $optionText);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is not selected.
     *
     * ``` php
     * <?php
     * $I->dontSeeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeOptionIsSelected()
     */
    abstract public function cantSeeOptionIsSelected($selector, $optionText);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is not selected.
     *
     * ``` php
     * <?php
     * $I->dontSeeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::dontSeeOptionIsSelected()
     */
    abstract public function dontSeeOptionIsSelected($selector, $optionText);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title contains the given string.
     *
     * ``` php
     * <?php
     * $I->seeInTitle('Blog - Post #1');
     * ?>
     * ```
     *
     * @param $title
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInTitle()
     */
    abstract public function canSeeInTitle($title);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title contains the given string.
     *
     * ``` php
     * <?php
     * $I->seeInTitle('Blog - Post #1');
     * ?>
     * ```
     *
     * @param $title
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::seeInTitle()
     */
    abstract public function seeInTitle($title);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title does not contain the given string.
     *
     * @param $title
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInTitle()
     */
    abstract public function cantSeeInTitle($title);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title does not contain the given string.
     *
     * @param $title
     *
     * @return mixed
     * @see \Codeception\Module\WebDriver::dontSeeInTitle()
     */
    abstract public function dontSeeInTitle($title);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Accepts the active JavaScript native popup window, as created by `window.alert`|`window.confirm`|`window.prompt`.
     * Don't confuse popups with modal windows,
     * as created by [various libraries](http://jster.net/category/windows-modals-popups).
     * @see \Codeception\Module\WebDriver::acceptPopup()
     */
    abstract public function acceptPopup();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Dismisses the active JavaScript popup, as created by `window.alert`, `window.confirm`, or `window.prompt`.
     * @see \Codeception\Module\WebDriver::cancelPopup()
     */
    abstract public function cancelPopup();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the active JavaScript popup,
     * as created by `window.alert`|`window.confirm`|`window.prompt`, contains the given string.
     *
     * @param $text
     *
     * @throws \Codeception\Exception\ModuleException
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::seeInPopup()
     */
    abstract public function canSeeInPopup($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the active JavaScript popup,
     * as created by `window.alert`|`window.confirm`|`window.prompt`, contains the given string.
     *
     * @param $text
     *
     * @throws \Codeception\Exception\ModuleException
     * @see \Codeception\Module\WebDriver::seeInPopup()
     */
    abstract public function seeInPopup($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the active JavaScript popup,
     * as created by `window.alert`|`window.confirm`|`window.prompt`, does NOT contain the given string.
     *
     * @param $text
     *
     * @throws \Codeception\Exception\ModuleException
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\WebDriver::dontSeeInPopup()
     */
    abstract public function cantSeeInPopup($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the active JavaScript popup,
     * as created by `window.alert`|`window.confirm`|`window.prompt`, does NOT contain the given string.
     *
     * @param $text
     *
     * @throws \Codeception\Exception\ModuleException
     * @see \Codeception\Module\WebDriver::dontSeeInPopup()
     */
    abstract public function dontSeeInPopup($text);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Enters text into a native JavaScript prompt popup, as created by `window.prompt`.
     *
     * @param $keys
     *
     * @throws \Codeception\Exception\ModuleException
     * @see \Codeception\Module\WebDriver::typeInPopup()
     */
    abstract public function typeInPopup($keys);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Reloads the current page.
     * @see \Codeception\Module\WebDriver::reloadPage()
     */
    abstract public function reloadPage();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Moves back in history.
     * @see \Codeception\Module\WebDriver::moveBack()
     */
    abstract public function moveBack();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Moves forward in history.
     * @see \Codeception\Module\WebDriver::moveForward()
     */
    abstract public function moveForward();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Submits the given form on the page, optionally with the given form
     * values.  Give the form fields values as an array. Note that hidden fields
     * can't be accessed.
     *
     * Skipped fields will be filled by their values from the page.
     * You don't need to click the 'Submit' button afterwards.
     * This command itself triggers the request to form's action.
     *
     * You can optionally specify what button's value to include
     * in the request with the last parameter as an alternative to
     * explicitly setting its value in the second parameter, as
     * button values are not otherwise included in the request.
     *
     * Examples:
     *
     * ``` php
     * <?php
     * $I->submitForm('#login', [
     *     'login' => 'davert',
     *     'password' => '123456'
     * ]);
     * // or
     * $I->submitForm('#login', [
     *     'login' => 'davert',
     *     'password' => '123456'
     * ], 'submitButtonName');
     *
     * ```
     *
     * For example, given this sample "Sign Up" form:
     *
     * ``` html
     * <form action="/sign_up">
     *     Login:
     *     <input type="text" name="user[login]" /><br/>
     *     Password:
     *     <input type="password" name="user[password]" /><br/>
     *     Do you agree to our terms?
     *     <input type="checkbox" name="user[agree]" /><br/>
     *     Select pricing plan:
     *     <select name="plan">
     *         <option value="1">Free</option>
     *         <option value="2" selected="selected">Paid</option>
     *     </select>
     *     <input type="submit" name="submitButton" value="Submit" />
     * </form>
     * ```
     *
     * You could write the following to submit it:
     *
     * ``` php
     * <?php
     * $I->submitForm(
     *     '#userForm',
     *     [
     *         'user[login]' => 'Davert',
     *         'user[password]' => '123456',
     *         'user[agree]' => true
     *     ],
     *     'submitButton'
     * );
     * ```
     * Note that "2" will be the submitted value for the "plan" field, as it is
     * the selected option.
     *
     * Also note that this differs from PhpBrowser, in that
     * ```'user' => [ 'login' => 'Davert' ]``` is not supported at the moment.
     * Named array keys *must* be included in the name as above.
     *
     * Pair this with seeInFormFields for quick testing magic.
     *
     * ``` php
     * <?php
     * $form = [
     *      'field1' => 'value',
     *      'field2' => 'another value',
     *      'checkbox1' => true,
     *      // ...
     * ];
     * $I->submitForm('//form[@id=my-form]', $form, 'submitButton');
     * // $I->amOnPage('/path/to/form-page') may be needed
     * $I->seeInFormFields('//form[@id=my-form]', $form);
     * ?>
     * ```
     *
     * Parameter values must be set to arrays for multiple input fields
     * of the same name, or multi-select combo boxes.  For checkboxes,
     * either the string value can be used, or boolean values which will
     * be replaced by the checkbox's value in the DOM.
     *
     * ``` php
     * <?php
     * $I->submitForm('#my-form', [
     *      'field1' => 'value',
     *      'checkbox' => [
     *          'value of first checkbox',
     *          'value of second checkbox,
     *      ],
     *      'otherCheckboxes' => [
     *          true,
     *          false,
     *          false
     *      ],
     *      'multiselect' => [
     *          'first option value',
     *          'second option value'
     *      ]
     * ]);
     * ?>
     * ```
     *
     * Mixing string and boolean values for a checkbox's value is not supported
     * and may produce unexpected results.
     *
     * Field names ending in "[]" must be passed without the trailing square
     * bracket characters, and must contain an array for its value.  This allows
     * submitting multiple values with the same name, consider:
     *
     * ```php
     * $I->submitForm('#my-form', [
     *     'field[]' => 'value',
     *     'field[]' => 'another value', // 'field[]' is already a defined key
     * ]);
     * ```
     *
     * The solution is to pass an array value:
     *
     * ```php
     * // this way both values are submitted
     * $I->submitForm('#my-form', [
     *     'field' => [
     *         'value',
     *         'another value',
     *     ]
     * ]);
     * ```
     *
     * The `$button` parameter can be either a string, an array or an instance
     * of Facebook\WebDriver\WebDriverBy. When it is a string, the
     * button will be found by its "name" attribute. If $button is an
     * array then it will be treated as a strict selector and a WebDriverBy
     * will be used verbatim.
     *
     * For example, given the following HTML:
     *
     * ``` html
     * <input type="submit" name="submitButton" value="Submit" />
     * ```
     *
     * `$button` could be any one of the following:
     *   - 'submitButton'
     *   - ['name' => 'submitButton']
     *   - WebDriverBy::name('submitButton')
     *
     * @param $selector
     * @param $params
     * @param $button
     * @see \Codeception\Module\WebDriver::submitForm()
     */
    abstract public function submitForm($selector, $params, $button = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits up to $timeout seconds for the given element to change.
     * Element "change" is determined by a callback function which is called repeatedly
     * until the return value evaluates to true.
     *
     * ``` php
     * <?php
     * use \Facebook\WebDriver\WebDriverElement
     * $I->waitForElementChange('#menu', function(WebDriverElement $el) {
     *     return $el->isDisplayed();
     * }, 100);
     * ?>
     * ```
     *
     * @param $element
     * @param \Closure $callback
     * @param int $timeout seconds
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::waitForElementChange()
     */
    abstract public function waitForElementChange($element, $callback, $timeout = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits up to $timeout seconds for an element to appear on the page.
     * If the element doesn't appear, a timeout exception is thrown.
     *
     * ``` php
     * <?php
     * $I->waitForElement('#agree_button', 30); // secs
     * $I->click('#agree_button');
     * ?>
     * ```
     *
     * @param $element
     * @param int $timeout seconds
     * @throws \Exception
     * @see \Codeception\Module\WebDriver::waitForElement()
     */
    abstract public function waitForElement($element, $timeout = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits up to $timeout seconds for the given element to be visible on the page.
     * If element doesn't appear, a timeout exception is thrown.
     *
     * ``` php
     * <?php
     * $I->waitForElementVisible('#agree_button', 30); // secs
     * $I->click('#agree_button');
     * ?>
     * ```
     *
     * @param $element
     * @param int $timeout seconds
     * @throws \Exception
     * @see \Codeception\Module\WebDriver::waitForElementVisible()
     */
    abstract public function waitForElementVisible($element, $timeout = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits up to $timeout seconds for the given element to become invisible.
     * If element stays visible, a timeout exception is thrown.
     *
     * ``` php
     * <?php
     * $I->waitForElementNotVisible('#agree_button', 30); // secs
     * ?>
     * ```
     *
     * @param $element
     * @param int $timeout seconds
     * @throws \Exception
     * @see \Codeception\Module\WebDriver::waitForElementNotVisible()
     */
    abstract public function waitForElementNotVisible($element, $timeout = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits up to $timeout seconds for the given string to appear on the page.
     *
     * Can also be passed a selector to search in, be as specific as possible when using selectors.
     * waitForText() will only watch the first instance of the matching selector / text provided.
     * If the given text doesn't appear, a timeout exception is thrown.
     *
     * ``` php
     * <?php
     * $I->waitForText('foo', 30); // secs
     * $I->waitForText('foo', 30, '.title'); // secs
     * ?>
     * ```
     *
     * @param string $text
     * @param int $timeout seconds
     * @param string $selector optional
     * @throws \Exception
     * @see \Codeception\Module\WebDriver::waitForText()
     */
    abstract public function waitForText($text, $timeout = null, $selector = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Wait for $timeout seconds.
     *
     * @param int|float $timeout secs
     * @throws \Codeception\Exception\TestRuntimeException
     * @see \Codeception\Module\WebDriver::wait()
     */
    abstract public function wait($timeout);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Low-level API method.
     * If Codeception commands are not enough, this allows you to use Selenium WebDriver methods directly:
     *
     * ``` php
     * $I->executeInSelenium(function(\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
     *   $webdriver->get('http://google.com');
     * });
     * ```
     *
     * This runs in the context of the
     * [RemoteWebDriver class](https://github.com/facebook/php-webdriver/blob/master/lib/remote/RemoteWebDriver.php).
     * Try not to use this command on a regular basis.
     * If Codeception lacks a feature you need, please implement it and submit a patch.
     *
     * @param callable $function
     * @see \Codeception\Module\WebDriver::executeInSelenium()
     */
    abstract public function executeInSelenium($function);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Switch to another window identified by name.
     *
     * The window can only be identified by name. If the $name parameter is blank, the parent window will be used.
     *
     * Example:
     * ``` html
     * <input type="button" value="Open window" onclick="window.open('http://example.com', 'another_window')">
     * ```
     *
     * ``` php
     * <?php
     * $I->click("Open window");
     * # switch to another window
     * $I->switchToWindow("another_window");
     * # switch to parent window
     * $I->switchToWindow();
     * ?>
     * ```
     *
     * If the window has no name, match it by switching to next active tab using `switchToNextTab` method.
     *
     * Or use native Selenium functions to get access to all opened windows:
     *
     * ``` php
     * <?php
     * $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
     *      $handles=$webdriver->getWindowHandles();
     *      $last_window = end($handles);
     *      $webdriver->switchTo()->window($last_window);
     * });
     * ?>
     * ```
     *
     * @param string|null $name
     * @see \Codeception\Module\WebDriver::switchToWindow()
     */
    abstract public function switchToWindow($name = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Switch to another frame on the page.
     *
     * Example:
     * ``` html
     * <iframe name="another_frame" src="http://example.com">
     *
     * ```
     *
     * ``` php
     * <?php
     * # switch to iframe
     * $I->switchToIFrame("another_frame");
     * # switch to parent page
     * $I->switchToIFrame();
     *
     * ```
     *
     * @param string|null $name
     * @see \Codeception\Module\WebDriver::switchToIFrame()
     */
    abstract public function switchToIFrame($name = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Executes JavaScript and waits up to $timeout seconds for it to return true.
     *
     * In this example we will wait up to 60 seconds for all jQuery AJAX requests to finish.
     *
     * ``` php
     * <?php
     * $I->waitForJS("return $.active == 0;", 60);
     * ?>
     * ```
     *
     * @param string $script
     * @param int $timeout seconds
     * @see \Codeception\Module\WebDriver::waitForJS()
     */
    abstract public function waitForJS($script, $timeout = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Executes custom JavaScript.
     *
     * This example uses jQuery to get a value and assigns that value to a PHP variable:
     *
     * ```php
     * <?php
     * $myVar = $I->executeJS('return $("#myField").val()');
     *
     * // additional arguments can be passed as array
     * // Example shows `Hello World` alert:
     * $I->executeJS("window.alert(arguments[0])", ['Hello world']);
     * ```
     *
     * @param $script
     * @param array $arguments
     * @return mixed
     * @see \Codeception\Module\WebDriver::executeJS()
     */
    abstract public function executeJS($script, $arguments = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Executes asynchronous JavaScript.
     * A callback should be executed by JavaScript to exit from a script.
     * Callback is passed as a last element in `arguments` array.
     * Additional arguments can be passed as array in second parameter.
     *
     * ```js
     * // wait for 1200 milliseconds my running `setTimeout`
     * * $I->executeAsyncJS('setTimeout(arguments[0], 1200)');
     *
     * $seconds = 1200; // or seconds are passed as argument
     * $I->executeAsyncJS('setTimeout(arguments[1], arguments[0])', [$seconds]);
     * ```
     *
     * @param $script
     * @param array $arguments
     * @return mixed
     * @see \Codeception\Module\WebDriver::executeAsyncJS()
     */
    abstract public function executeAsyncJS($script, $arguments = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Maximizes the current window.
     * @see \Codeception\Module\WebDriver::maximizeWindow()
     */
    abstract public function maximizeWindow();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Performs a simple mouse drag-and-drop operation.
     *
     * ``` php
     * <?php
     * $I->dragAndDrop('#drag', '#drop');
     * ?>
     * ```
     *
     * @param string $source (CSS ID or XPath)
     * @param string $target (CSS ID or XPath)
     * @see \Codeception\Module\WebDriver::dragAndDrop()
     */
    abstract public function dragAndDrop($source, $target);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Move mouse over the first element matched by the given locator.
     * If the first parameter null then the page is used.
     * If the second and third parameters are given,
     * then the mouse is moved to an offset of the element's top-left corner.
     * Otherwise, the mouse is moved to the center of the element.
     *
     * ``` php
     * <?php
     * $I->moveMouseOver(['css' => '.checkout']);
     * $I->moveMouseOver(null, 20, 50);
     * $I->moveMouseOver(['css' => '.checkout'], 20, 50);
     * ?>
     * ```
     *
     * @param string $cssOrXPath css or xpath of the web element
     * @param int $offsetX
     * @param int $offsetY
     *
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::moveMouseOver()
     */
    abstract public function moveMouseOver($cssOrXPath = null, $offsetX = null, $offsetY = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Performs click with the left mouse button on an element.
     * If the first parameter `null` then the offset is relative to the actual mouse position.
     * If the second and third parameters are given,
     * then the mouse is moved to an offset of the element's top-left corner.
     * Otherwise, the mouse is moved to the center of the element.
     *
     * ``` php
     * <?php
     * $I->clickWithLeftButton(['css' => '.checkout']);
     * $I->clickWithLeftButton(null, 20, 50);
     * $I->clickWithLeftButton(['css' => '.checkout'], 20, 50);
     * ?>
     * ```
     *
     * @param string $cssOrXPath css or xpath of the web element (body by default).
     * @param int $offsetX
     * @param int $offsetY
     *
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::clickWithLeftButton()
     */
    abstract public function clickWithLeftButton($cssOrXPath = null, $offsetX = null, $offsetY = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Performs contextual click with the right mouse button on an element.
     * If the first parameter `null` then the offset is relative to the actual mouse position.
     * If the second and third parameters are given,
     * then the mouse is moved to an offset of the element's top-left corner.
     * Otherwise, the mouse is moved to the center of the element.
     *
     * ``` php
     * <?php
     * $I->clickWithRightButton(['css' => '.checkout']);
     * $I->clickWithRightButton(null, 20, 50);
     * $I->clickWithRightButton(['css' => '.checkout'], 20, 50);
     * ?>
     * ```
     *
     * @param string $cssOrXPath css or xpath of the web element (body by default).
     * @param int    $offsetX
     * @param int    $offsetY
     *
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::clickWithRightButton()
     */
    abstract public function clickWithRightButton($cssOrXPath = null, $offsetX = null, $offsetY = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Pauses test execution in debug mode.
     * To proceed test press "ENTER" in console.
     *
     * This method is useful while writing tests,
     * since it allows you to inspect the current page in the middle of a test case.
     * @see \Codeception\Module\WebDriver::pauseExecution()
     */
    abstract public function pauseExecution();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Performs a double-click on an element matched by CSS or XPath.
     *
     * @param $cssOrXPath
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::doubleClick()
     */
    abstract public function doubleClick($cssOrXPath);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Presses the given key on the given element.
     * To specify a character and modifier (e.g. ctrl, alt, shift, meta), pass an array for $char with
     * the modifier as the first element and the character as the second.
     * For special keys use key constants from WebDriverKeys class.
     *
     * ``` php
     * <?php
     * // <input id="page" value="old" />
     * $I->pressKey('#page','a'); // => olda
     * $I->pressKey('#page',array('ctrl','a'),'new'); //=> new
     * $I->pressKey('#page',array('shift','111'),'1','x'); //=> old!!!1x
     * $I->pressKey('descendant-or-self::*[@id='page']','u'); //=> oldu
     * $I->pressKey('#name', array('ctrl', 'a'), \Facebook\WebDriver\WebDriverKeys::DELETE); //=>''
     * ?>
     * ```
     *
     * @param $element
     * @param $char string|array Can be char or array with modifier. You can provide several chars.
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::pressKey()
     */
    abstract public function pressKey($element, $char);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Append the given text to the given element.
     * Can also add a selection to a select box.
     *
     * ``` php
     * <?php
     * $I->appendField('#mySelectbox', 'SelectValue');
     * $I->appendField('#myTextField', 'appended');
     * ?>
     * ```
     *
     * @param string $field
     * @param string $value
     * @throws \Codeception\Exception\ElementNotFound
     * @see \Codeception\Module\WebDriver::appendField()
     */
    abstract public function appendField($field, $value);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param string $name
     * @see \Codeception\Module\WebDriver::saveSessionSnapshot()
     */
    abstract public function saveSessionSnapshot($name);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param string $name
     * @return bool
     * @see \Codeception\Module\WebDriver::loadSessionSnapshot()
     */
    abstract public function loadSessionSnapshot($name);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Move to the middle of the given element matched by the given locator.
     * Extra shift, calculated from the top-left corner of the element,
     * can be set by passing $offsetX and $offsetY parameters.
     *
     * ``` php
     * <?php
     * $I->scrollTo(['css' => '.checkout'], 20, 50);
     * ?>
     * ```
     *
     * @param $selector
     * @param int $offsetX
     * @param int $offsetY
     * @see \Codeception\Module\WebDriver::scrollTo()
     */
    abstract public function scrollTo($selector, $offsetX = null, $offsetY = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Opens a new browser tab (wherever it is possible) and switches to it.
     *
     * ```php
     * <?php
     * $I->openNewTab();
     * ```
     * Tab is opened by using `window.open` javascript in a browser.
     * Please note, that adblock can restrict creating such tabs.
     *
     * Can't be used with PhantomJS
     *
     * @see \Codeception\Module\WebDriver::openNewTab()
     */
    abstract public function openNewTab();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Closes current browser tab and switches to previous active tab.
     *
     * ```php
     * <?php
     * $I->closeTab();
     * ```
     *
     * Can't be used with PhantomJS
     * @see \Codeception\Module\WebDriver::closeTab()
     */
    abstract public function closeTab();


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Switches to next browser tab.
     * An offset can be specified.
     *
     * ```php
     * <?php
     * // switch to next tab
     * $I->switchToNextTab();
     * // switch to 2nd next tab
     * $I->switchToNextTab(2);
     * ```
     *
     * Can't be used with PhantomJS
     *
     * @param int $offset 1
     * @see \Codeception\Module\WebDriver::switchToNextTab()
     */
    abstract public function switchToNextTab($offset = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Switches to previous browser tab.
     * An offset can be specified.
     *
     * ```php
     * <?php
     * // switch to previous tab
     * $I->switchToPreviousTab();
     * // switch to 2nd previous tab
     * $I->switchToPreviousTab(2);
     * ```
     *
     * Can't be used with PhantomJS
     *
     * @param int $offset 1
     * @see \Codeception\Module\WebDriver::switchToPreviousTab()
     */
    abstract public function switchToPreviousTab($offset = null);


    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Waits for element and runs a sequence of actions inside its context.
     * Actions can be defined with array, callback, or `Codeception\Util\ActionSequence` instance.
     *
     * Actions as array are recommended for simple to combine "waitForElement" with assertions;
     * `waitForElement($el)` and `see('text', $el)` can be simplified to:
     *
     * ```php
     * <?php
     * $I->performOn($el, ['see' => 'text']);
     * ```
     *
     * List of actions can be pragmatically build using `Codeception\Util\ActionSequence`:
     *
     * ```php
     * <?php
     * $I->performOn('.model', ActionSequence::build()
     *     ->see('Warning')
     *     ->see('Are you sure you want to delete this?')
     *     ->click('Yes')
     * );
     * ```
     *
     * Actions executed from array or ActionSequence will print debug output for actions, and adds an action name to
     * exception on failure.
     *
     * Whenever you need to define more actions a callback can be used. A WebDriver module is passed for argument:
     *
     * ```php
     * <?php
     * $I->performOn('.rememberMe', function (WebDriver $I) {
     *      $I->see('Remember me next time');
     *      $I->seeElement('#LoginForm_rememberMe');
     *      $I->dontSee('Login');
     * });
     * ```
     *
     * In 3rd argument you can set number a seconds to wait for element to appear
     *
     * @param $element
     * @param $actions
     * @param int $timeout
     * @see \Codeception\Module\WebDriver::performOn()
     */
    abstract public function performOn($element, $actions, $timeout = null);

    abstract public function putShot(string $name, string $rawPNG);

    abstract public function getShot(string $name) : string;

    abstract public function uploadShots();
}
