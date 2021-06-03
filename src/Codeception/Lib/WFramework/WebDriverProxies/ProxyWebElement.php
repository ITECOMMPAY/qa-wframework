<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 27.02.19
 * Time: 17:20
 */

namespace Codeception\Lib\WFramework\WebDriverProxies;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\FileDetector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\WebDriverPoint;


class ProxyWebElement extends RemoteWebElement
{
    /** @var RemoteWebElement|null */
    protected $remoteWebElement = null;

    /** @var RemoteWebDriver */
    protected $webDriver = null;

    /** @var WLocator */
    protected $locator = null;

    protected $timeout = 5;

    /** @var ProxyWebElement */
    protected $parentElement = null;

    protected $uid;

    protected $acceptedChildren = [];

    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(WLocator $locator, RemoteWebDriver $webDriver, int $timeout = 5, ProxyWebElement $parentElement = null)
    {
        //здесь нельзя вызывать родительский конструктор
        $this->webDriver = $webDriver;
        $this->locator = $locator;
        $this->timeout = $timeout;
        $this->parentElement = $parentElement;

        $this->uid = bin2hex(random_bytes(32));
    }

    public function staled() : bool
    {
        if (!$this->remoteWebElement instanceof RemoteWebElement)
        {
            if ($this->parentElement !== null)
            {
                $this->parentElement->doesntKnowMe($this->uid);
            }

            return true;
        }

        if ($this->parentElement !== null && $this->parentElement->doesntKnowMe($this->uid))
        {
            $this->forgetChildren();
            return true;
        }

        try
        {
            return $this->remoteWebElement->isEnabled() === null;
        }
        catch (NoSuchElementException $e)
        {
            $this->forgetChildren();
            return true;
        }
        catch (StaleElementReferenceException $e)
        {
            $this->forgetChildren();
            return true;
        }
    }

    protected function forgetChildren()
    {
        $this->acceptedChildren = [];
    }

    public function doesntKnowMe(string $myUid) : bool
    {
        if (isset($this->acceptedChildren[$myUid]))
        {
            return false;
        }

        $this->acceptedChildren[$myUid] = true;

        return true;
    }

    /**
     * @return RemoteWebElement
     * @throws NoSuchElementException
     * @throws StaleElementReferenceException
     */
    public function returnRemoteWebElement() : RemoteWebElement
    {
        $this->refresh();

        return $this->remoteWebElement;
    }

    protected function refresh()
    {
        if (!$this->staled())
        {
            return;
        }

        $locatorMechanism = $this->locator->getMechanism();
        $locatorValue = $this->locator->getValue();

        WLogger::logDebug($this, "Ищем элемент с $locatorMechanism: '$locatorValue'");

        $this->remoteWebElement = $this->getNewRemoteWebElement();
    }

    protected function getNewRemoteWebElement() : RemoteWebElement
    {
        if ($this->parentElement !== null)
        {
            return $this->parentElement->returnRemoteWebElement()->findElement($this->locator);
        }

        return $this->webDriver->findElement($this->locator);
    }

    public function setRemoteWebElement(RemoteWebElement $remoteWebElement)
    {
        $this->remoteWebElement = $remoteWebElement;
    }

    public function getLocator() : WLocator
    {
        return $this->locator;
    }

    public function getParentElement()
    {
        return $this->parentElement;
    }

    public function executeScript(string $script, array $arguments = [])
    {
        return $this->webDriver->executeScript($script, $arguments);
    }

    public function executeScriptOnThis(string $script, array $arguments = [])
    {
        $this->refresh();

        array_unshift($arguments, $this);

        return $this->webDriver->executeScript($script, $arguments);
    }

    public function executeActions() : ProxyWebElementActions
    {
        $this->refresh();

        return new ProxyWebElementActions(new WebDriverActions($this->webDriver), $this);
    }

    public function isExist() : bool
    {
        try
        {
            if ($this->parentElement !== null)
            {
                if (!$this->parentElement->isExist())
                {
                    return false;
                }

                $this->parentElement->returnRemoteWebElement()->findElement($this->locator);
            }
            else
            {
                $this->webDriver->findElement($this->locator);
            }

        }
        catch (NoSuchElementException $e)
        {
            return false;
        }
        catch (StaleElementReferenceException $e)
        {
            return false;
        }

        return true;
    }

    public function findElement(WebDriverBy $by) : ProxyWebElement
    {
        return new ProxyWebElement(WLocator::fromWebDriverBy($by), $this->webDriver, $this->timeout, $this);
    }

    public function findElements(WebDriverBy $by) : array
    {
        return $this->findProxyWebElements(WLocator::fromWebDriverBy($by))->refresh()->getElementsArray();
    }

    public function findProxyWebElements(WLocator $by) : ProxyWebElements
    {
        return new ProxyWebElements($by, $this->webDriver, $this->timeout, $this);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function clear() : ProxyWebElement
    {
        $this->returnRemoteWebElement()->clear();
        return $this;
    }

    public function click() : ProxyWebElement
    {
        $this->returnRemoteWebElement()->click();
        return $this;
    }

    /**
     * @param string $attribute_name
     * @return string|null
     * @throws NoSuchElementException
     * @throws StaleElementReferenceException
     */
    public function getAttribute($attribute_name)
    {
        return $this->returnRemoteWebElement()->getAttribute($attribute_name);
    }

    public function getCSSValue($css_property_name) : string
    {
        return $this->returnRemoteWebElement()->getCSSValue($css_property_name);
    }

    public function getLocation() : WebDriverPoint
    {
        return ProxyWebDriverPoint::fromWebDriverPoint($this->returnRemoteWebElement()->getLocation());
    }

    public function getLocationOnScreenOnceScrolledIntoView() : WebDriverPoint
    {
        return ProxyWebDriverPoint::fromWebDriverPoint($this->returnRemoteWebElement()->getLocationOnScreenOnceScrolledIntoView());
    }

    public function getCoordinates() : WebDriverCoordinates
    {
        return $this->returnRemoteWebElement()->getCoordinates();
    }

    public function getSize() : WebDriverDimension
    {
        return ProxyWebDriverDimension::fromWebDriverDimension($this->returnRemoteWebElement()->getSize());
    }

    public function getTagName() : string
    {
        return $this->returnRemoteWebElement()->getTagName();
    }

    public function getText() : string
    {
        return $this->returnRemoteWebElement()->getText();
    }

    /**
     * @return bool|null
     * @throws NoSuchElementException
     * @throws StaleElementReferenceException
     */
    public function isDisplayed()
    {
        return $this->returnRemoteWebElement()->isDisplayed();
    }

    public function isEnabled() : bool
    {
        return $this->returnRemoteWebElement()->isEnabled();
    }

    public function isSelected() : bool
    {
        return $this->returnRemoteWebElement()->isSelected();
    }

    public function sendKeys($value) : ProxyWebElement
    {
        try
        {
            $this->returnRemoteWebElement()->sendKeys($value);
        }
        catch (\Facebook\WebDriver\Exception\ElementNotInteractableException $e)
        {
            WLogger::logWarning($this, "Элемент не доступен для взаимодействия: " . $this->locator);

            throw $e;
        }

        return $this;
    }

    public function setFileDetector(FileDetector $detector) : ProxyWebElement
    {
        $this->returnRemoteWebElement()->setFileDetector($detector);
        return $this;
    }

    public function submit() : ProxyWebElement
    {
        $this->returnRemoteWebElement()->submit();
        return $this;
    }

    public function getID() : string
    {
        return $this->returnRemoteWebElement()->getID();
    }

    public function equals(WebDriverElement $other) : bool
    {
        return $this->returnRemoteWebElement()->equals($other);
    }

    protected function newElement($id)
    {
        return $this->returnRemoteWebElement()->newElement($id);
    }

    protected function upload($local_file) : string
    {
        return $this->returnRemoteWebElement()->upload($local_file);
    }

    public function takeElementScreenshot($save_as = null)
    {
        return $this->returnRemoteWebElement()->takeElementScreenshot($save_as);
    }
}
