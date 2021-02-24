<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 12.03.19
 * Time: 18:04
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WElement\Import;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\EmptyComposite;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebDriver;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;

class WFrom
{
    protected $name = '';

    protected $locator = null;

    protected $relative = false;

    /**
     * @var ProxyWebElement|null
     */
    protected $proxyWebElement = null;

    /** @var ProxyWebDriver|null  */
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

    public function getProxyWebElement()
    {
        return $this->proxyWebElement;
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

    public static function proxyWebElement(string $instanceName, ProxyWebElement $proxyWebElement, WPageObject $parent) : WFrom
    {
        return new WFromProxyWebElement($instanceName, $proxyWebElement, $parent);
    }

    public static function anotherWElement(WElement $element) : WFrom
    {
        return new WFromAnotherWElement($element);
    }
}
