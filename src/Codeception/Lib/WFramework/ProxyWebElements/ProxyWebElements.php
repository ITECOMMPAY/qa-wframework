<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 05.03.19
 * Time: 15:47
 */

namespace Codeception\Lib\WFramework\ProxyWebElements;


use Codeception\Lib\WFramework\Debug\DebugInfo;
use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\ProxyWebElement\ProxyWebElement;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Countable;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Iterator;
use function count;
use function trim;

class ProxyWebElements implements Iterator, Countable
{
    protected $webDriver = null;

    protected $locator = null;

    protected $timeout = 5;

    protected $parentElement = null;

    protected $elementsArray = array();

    /** @var ProxyWebElementsListener[]  */
    protected $listeners = array();

    /** @var DebugInfo|null  */
    protected $debugInfo = null;

    public function __construct(WLocator $locator, RemoteWebDriver $webDriver, int $timeout = 5, ProxyWebElement $parentElement = null)
    {
        $this->webDriver = $webDriver;
        $this->locator = $locator;
        $this->timeout = $timeout;
        $this->parentElement = $parentElement;
    }

    /**
     * @return ProxyWebElement[]
     */
    public function getElementsArray() : array
    {
        return $this->elementsArray;
    }

    public function getLocator() : WLocator
    {
        return $this->locator;
    }

    public function getParentElement()
    {
        return $this->parentElement;
    }

    public function refresh() : ProxyWebElements
    {
        $locatorMechanism = $this->locator->getMechanism();
        $locatorValue = $this->locator->getValue();

        WLogger::logDebug("Обновляем коллекцию элементов, с $locatorMechanism: '$locatorValue'");

        $elements = $this->findThis();

        $count = count($elements);

        $result = array();

        if ($this->locator->getMechanism() === 'xpath')
        {
            for ($i = 1; $i <= $count; $i++)
            {
                $locator = WLocator::xpath('(' . $this->getFullXPath() . ")[$i]");
                $proxyWebElement = new ProxyWebElement($locator, $this->webDriver, $this->timeout);
                $proxyWebElement->setRemoteWebElement($elements[$i - 1]);

                if ($this->hasDebugInfo())
                {
                    $proxyWebElement->setDebugInfo($this->debugInfo);
                }

                $result[] = $proxyWebElement;
            }
        }
        else
        {
            throw new UsageException('Коллекции элементов (WCollection, WArray и т.п.) работают только с XPath!');
        }

        $this->elementsArray = $result;
        $this->listenersNotify();

        return $this;
    }

    protected function getFullXPath() : string
    {
        $element = $this;
        $result = '';

        while ($element !== null)
        {
            $locator = trim($element->getLocator()->getValue());

            if (isset($locator[0]) && mb_strpos($locator, '.') === 0)
            {
                $locator = mb_substr($locator, 1);
            }

            $result = $locator . $result;

            $element = $element->getParentElement();
        }

        return $result;
    }

    public function setDebugInfo(DebugInfo $debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }

    public function hasDebugInfo() : bool
    {
        return $this->debugInfo !== null;
    }

    public function listenerAdd(ProxyWebElementsListener $listener) : ProxyWebElements
    {
        $hash = spl_object_hash($listener);
        $this->listeners[$hash] = $listener;
        return $this;
    }

    public function listenerRemove(ProxyWebElementsListener $listener) : ProxyWebElements
    {
        $hash = spl_object_hash($listener);
        unset($this->listeners[$hash]);
        return $this;
    }

    private function listenersNotify() : ProxyWebElements
    {
        foreach ($this->listeners as $listener)
        {
            $listener->onProxyWebElementsRefresh();
        }

        return $this;
    }

    /**
     * @return RemoteWebElement[]
     */
    protected function findThis() : array
    {
        if ($this->parentElement === null)
        {
            try
            {
                return $this->webDriver->findElements($this->locator);
            }
            catch (NoSuchElementException $e)
            {
                return [];
            }
            catch (StaleElementReferenceException $e)
            {
                return [];
            }
        }

        if (!$this->parentElement->isExist())
        {
            return [];
        }

        return $this->parentElement->returnRemoteWebElement()->findElements($this->locator);
    }

    //------------------------------------------------------------------------------------------------------------------

    private $currentPosition = 0;

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() : ProxyWebElement
    {
        return $this->elementsArray[$this->currentPosition];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->currentPosition++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() : int
    {
        return $this->currentPosition;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() : bool
    {
        return isset($this->elementsArray[$this->currentPosition]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->currentPosition = 0;
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() : int
    {
        return count($this->elementsArray);
    }
}
