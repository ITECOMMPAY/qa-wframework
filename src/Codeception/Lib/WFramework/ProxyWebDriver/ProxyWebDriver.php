<?php


namespace Codeception\Lib\WFramework\ProxyWebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCommandExecutor;

class ProxyWebDriver extends RemoteWebDriver
{
    /**
     * @var RemoteWebDriver
     */
    protected $remoteWebDriver;

    public function __construct()
    {
        //здесь нельзя вызывать родительский конструктор
    }

    public function setRemoteWebDriver(RemoteWebDriver $remoteWebDriver) : ProxyWebDriver
    {
        $this->remoteWebDriver = $remoteWebDriver;
        return $this;
    }

    public function initialized() : bool
    {
        return $this->remoteWebDriver !== null;
    }

    public static function create($selenium_server_url = 'http://localhost:4444/wd/hub', $desired_capabilities = null, $connection_timeout_in_ms = null, $request_timeout_in_ms = null, $http_proxy = null, $http_proxy_port = null, DesiredCapabilities $required_capabilities = null)
    {
        return parent::create($selenium_server_url, $desired_capabilities, $connection_timeout_in_ms, $request_timeout_in_ms, $http_proxy, $http_proxy_port, $required_capabilities); 
    }

    public static function createBySessionID($session_id, $selenium_server_url = 'http://localhost:4444/wd/hub', $connection_timeout_in_ms = null, $request_timeout_in_ms = null)
    {
        return parent::createBySessionID($session_id, $selenium_server_url, $connection_timeout_in_ms, $request_timeout_in_ms); 
    }

    public function close()
    {
        return $this->remoteWebDriver->close();
    }

    public function findElement(WebDriverBy $by)
    {
        return $this->remoteWebDriver->findElement($by);
    }

    public function findElements(WebDriverBy $by)
    {
        return $this->remoteWebDriver->findElements($by);
    }

    public function get($url)
    {
        return $this->remoteWebDriver->get($url);
    }

    public function getCurrentURL()
    {
        return $this->remoteWebDriver->getCurrentURL();
    }

    public function getPageSource()
    {
        return $this->remoteWebDriver->getPageSource();
    }

    public function getTitle()
    {
        return $this->remoteWebDriver->getTitle();
    }

    public function getWindowHandle()
    {
        return $this->remoteWebDriver->getWindowHandle();
    }

    public function getWindowHandles()
    {
        return $this->remoteWebDriver->getWindowHandles();
    }

    public function quit()
    {
        $this->remoteWebDriver->quit();
    }

    public function executeScript($script, array $arguments = [])
    {
        return $this->remoteWebDriver->executeScript($script, $arguments);
    }

    public function executeAsyncScript($script, array $arguments = [])
    {
        return $this->remoteWebDriver->executeAsyncScript($script, $arguments); 
    }

    public function takeScreenshot($save_as = null)
    {
        return $this->remoteWebDriver->takeScreenshot($save_as); 
    }

    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250)
    {
        return $this->remoteWebDriver->wait($timeout_in_second, $interval_in_millisecond); 
    }

    public function manage()
    {
        return $this->remoteWebDriver->manage(); 
    }

    public function navigate()
    {
        return $this->remoteWebDriver->navigate(); 
    }

    public function switchTo()
    {
        return $this->remoteWebDriver->switchTo(); 
    }

    public function getMouse()
    {
        return $this->remoteWebDriver->getMouse(); 
    }

    public function getKeyboard()
    {
        return $this->remoteWebDriver->getKeyboard(); 
    }

    public function getTouch()
    {
        return $this->remoteWebDriver->getTouch(); 
    }

    public function action()
    {
        return $this->remoteWebDriver->action(); 
    }

    public function setCommandExecutor(WebDriverCommandExecutor $executor)
    {
        return $this->remoteWebDriver->setCommandExecutor($executor); 
    }

    public function getCommandExecutor()
    {
        return $this->remoteWebDriver->getCommandExecutor(); 
    }

    public function setSessionID($session_id)
    {
        return $this->remoteWebDriver->setSessionID($session_id); 
    }

    public function getSessionID()
    {
        return $this->remoteWebDriver->getSessionID(); 
    }

    public function getCapabilities()
    {
        return $this->remoteWebDriver->getCapabilities(); 
    }

    public static function getAllSessions($selenium_server_url = 'http://localhost:4444/wd/hub', $timeout_in_ms = 30000)
    {
        return parent::getAllSessions($selenium_server_url, $timeout_in_ms); 
    }

    public function execute($command_name, $params = [])
    {
        return $this->remoteWebDriver->execute($command_name, $params); 
    }
}
