<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 15:50
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;


use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\FacadeWebElements\FacadeWebElements;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class WsFrom
{
    protected $instanceName = '';

    protected $locator = null;

    protected $relative = false;

    protected $elementClass = '';

    protected $facadeWebElements = null;

    /** @var RemoteWebDriver|null  */
    protected $webDriver = null;

    /** @var WPageObject|null  */
    protected $parentElement = null;


    public function getFacadeWebElements()
    {
        return $this->facadeWebElements;
    }

    public function getInstanceName()
    {
        return $this->instanceName;
    }

    public function getLocator()
    {
        return $this->locator;
    }

    public function getRelative()
    {
        return $this->relative;
    }

    public function getElementClass()
    {
        return $this->elementClass;
    }

    public function getParent()
    {
        if ($this->parentElement === null)
        {
            $this->parentElement = EmptyComposite::get();
        }

        return $this->parentElement;
    }

    public function __construct()
    {
        throw new UsageException(
            PHP_EOL . 'Наследника WCollection нельзя напрямую использовать в степах (в т.ч. прописывать в методе _inject) 
                                - он должен располагаться на каком-нибудь WBlock, и именно WBlock должен быть прописан в степах.');
    }

    public static function facadeWebElements(string $name, FacadeWebElements $facadeWebElements, string $elementClass) : WsFrom
    {
        return new WsFromFacadeWebElements($name, $facadeWebElements, $elementClass);
    }

    public static function firstElement(WElement $webElement) : WsFrom
    {
        return new WsFromFirstElement($webElement);
    }
}
