<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.03.19
 * Time: 18:04
 */

namespace Common\Module\WFramework\WebObjects\Base\WElement\Import;


use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\Helpers\EmptyComposite;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;
use Common\Module\WFramework\WebObjects\Base\WPageObject;
use Common\Module\WFramework\WLocator\WLocator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class WFrom
{
    protected $name = '';

    protected $locator = null;

    protected $relative = false;

    protected $facadeWebElement = null;

    /** @var RemoteWebDriver|null  */
    protected $webDriver = null;

    /** @var WPageObject|null  */
    protected $parentElement = null;

    public function getName()
    {
        return $this->name;
    }

    public function getLocator()
    {
        return $this->locator;
    }

    public function getRelative()
    {
        return $this->relative;
    }

    public function getFacadeWebElement()
    {
        return $this->facadeWebElement;
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
        //Этот конструктор не нужно использовать при нормальном использовании фреймворка. В него может залезть
        //сам Codeception - и это знак того, что WElement был по ошибке объявлен в _inject() методе.

        throw new UsageException(
            PHP_EOL . 'Наследника WElement нельзя напрямую использовать в степах (в т.ч. прописывать в методе _inject) 
                                - он должен располагаться на каком-нибудь WBlock, и именно WBlock должен быть прописан в степах.');
    }

    public static function locator(string $instanceName, WLocator $locator, bool $relative = true) : WFrom
    {
        return new WFromLocator($instanceName, $locator, $relative);
    }

    public static function facadeWebElement(string $instanceName, FacadeWebElement $facadeWebElement) : WFrom
    {
        return new WFromFacadeWebElement($instanceName, $facadeWebElement);
    }

    public static function anotherWElement(WElement $element) : WFrom
    {
        return new WFromAnotherWElement($element);
    }
}
