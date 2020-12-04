<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 12:35
 */

namespace Codeception\Lib\WFramework\FacadeWebElements;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;
use Codeception\Lib\WFramework\FacadeWebElements\Import\FsFrom;
use Codeception\Lib\WFramework\FacadeWebElements\Operations\Groups\GetGroup;
use Codeception\Lib\WFramework\FacadeWebElements\Operations\Groups\IsGroup;
use Codeception\Lib\WFramework\FacadeWebElements\Operations\Groups\WaitGroup;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\ProxyWebElements\ProxyWebElements;
use Codeception\Lib\WFramework\FacadeWebElement\Import\FFrom as FacadeWebElementFrom;
use Codeception\Lib\WFramework\ProxyWebElements\ProxyWebElementsListener;
use Codeception\Lib\WFramework\WLocator\WLocator;
use Countable;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use function in_array;
use Iterator;

class FacadeWebElements implements Iterator, Countable, ProxyWebElementsListener
{
    protected $proxyWebElements = null;

    protected $elementsArray = array();

    private $allElementsArray = array();

    /** @var FacadeWebElementsListener[]  */
    protected $listeners = array();

    /** @var Cond|null  */
    protected $elementFilter = null;

    /** @var Cond[] */
    protected $elementFilters = array();

    public static function fromLocator(WLocator $locator, RemoteWebDriver $webDriver, FacadeWebElement $parentElement = null)
    {
        return new static(FsFrom::locator($locator, $webDriver, $parentElement));
    }

    public static function fromProxyWebElements(ProxyWebElements $proxyWebElements)
    {
        return new static(FsFrom::proxyWebElements($proxyWebElements));
    }

    public function __construct(FsFrom $importer)
    {
        $this->proxyWebElements = $importer->getProxyWebElements();
        $this->proxyWebElements->listenerAdd($this);
    }

    public function returnProxyWebElements() : ProxyWebElements
    {
        return $this->proxyWebElements;
    }

    /**
     * @return FacadeWebElement[]
     */
    public function getElementsArray() : array
    {
        return $this->elementsArray;
    }

    public function refresh() : FacadeWebElements
    {
        WLogger::logDebug('Обновляем коллекцию элементов');

        $this->proxyWebElements->refresh();

        return $this;
    }

    private function fillFrom(ProxyWebElements $proxyWebElements) : FacadeWebElements
    {
        $this->elementsArray = array();

        $proxyWebElementsArray = $proxyWebElements->getElementsArray();

        $this->elementsArray = array();
        $this->allElementsArray = array();

        foreach ($proxyWebElementsArray as $proxyWebElement)
        {
            $facadeWebElement = new FacadeWebElement(FacadeWebElementFrom::proxyWebElement($proxyWebElement));

            $this->allElementsArray[] = $facadeWebElement;

            if ($this->elementFilter !== null && $facadeWebElement->checkIt()->isNot($this->elementFilter))
            {
                continue;
            }

            $this->elementsArray[] = $facadeWebElement;
        }

        $this->listenersNotify();

        return $this;
    }

    public function listenerAdd(FacadeWebElementsListener $listener) : FacadeWebElements
    {
        $hash = spl_object_hash($listener);
        $this->listeners[$hash] = $listener;
        return $this;
    }

    public function listenerRemove(FacadeWebElementsListener $listener) : FacadeWebElements
    {
        $hash = spl_object_hash($listener);
        unset($this->listeners[$hash]);
        return $this;
    }

    private function listenersNotify() : FacadeWebElements
    {
        foreach ($this->listeners as $listener)
        {
            $listener->onFacadeWebElementsRefresh();
        }

        return $this;
    }

    public function onProxyWebElementsRefresh()
    {
        $this->fillFrom($this->proxyWebElements);
    }

    public function filtersSet(Cond $elementFilter) : FacadeWebElements
    {
        $this->elementFilter = $elementFilter;

        $this->elementsArray = array();

        foreach ($this->allElementsArray as $facadeWebElement)
        {
            if ($facadeWebElement->checkIt()->isNot($this->elementFilter))
            {
                continue;
            }

            $this->elementsArray[] = $facadeWebElement;
        }

        $this->listenersNotify();

        return $this;
    }

    /**
     * @return Cond|null
     */
    public function filtersGet()
    {
        return $this->elementFilter;
    }

    public function filterAdd(Cond $elementFilter) : FacadeWebElements
    {
        $this->elementFilters[] = $elementFilter;

        $elementFilter = Cond::and(...$this->elementFilters);

        $this->filtersSet($elementFilter);

        return $this;
    }

    public function filterPop() : FacadeWebElements
    {
        array_pop($this->elementFilters);

        $elementFilter = Cond::and(...$this->elementFilters);

        $this->filtersSet($elementFilter);

        return $this;
    }

    public function filtersRemove() : FacadeWebElements
    {
        $this->elementFilter = null;

        $this->elementsArray = $this->allElementsArray;

        $this->listenersNotify();

        return $this;
    }

    public function get() : GetGroup
    {
        return $this->get ?? $this->get = new GetGroup($this);
    }

    public function checkIt() : IsGroup
    {
        return $this->is ?? $this->is = new IsGroup($this);
    }

    public function wait() : WaitGroup
    {
        return $this->wait ?? $this->wait = new WaitGroup($this);
    }

    //------------------------------------------------------------------------------------------------------------------

    private $currentPosition = 0;

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() : FacadeWebElement
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
