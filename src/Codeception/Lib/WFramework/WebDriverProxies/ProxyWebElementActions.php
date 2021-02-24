<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 11:46
 */

namespace Codeception\Lib\WFramework\WebDriverProxies;


use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverElement;

class ProxyWebElementActions
{
    protected $webDriverActions;

    protected $proxyWebElement;
    
    public function __construct(WebDriverActions $webDriverActions, ProxyWebElement $proxyWebElement)
    {
        $this->webDriverActions = $webDriverActions;
        
        $this->proxyWebElement = $proxyWebElement;
    }
    
    //------------------------------------------------------------------------------------------------------------------
    public function perform()
    {
        $this->webDriverActions->perform(); 
    }

    public function click() : ProxyWebElementActions
    {
        $this->webDriverActions->click($this->proxyWebElement);

        return $this;
    }

    public function clickAndHold() : ProxyWebElementActions
    {
        $this->webDriverActions->clickAndHold($this->proxyWebElement);

        return $this;
    }

    public function contextClick() : ProxyWebElementActions
    {
        $this->webDriverActions->contextClick($this->proxyWebElement);

        return $this;
    }

    public function doubleClick() : ProxyWebElementActions
    {
        $this->webDriverActions->doubleClick($this->proxyWebElement);

        return $this;
    }

    public function dragAndDrop(WebDriverElement $target) : ProxyWebElementActions
    {
        $this->webDriverActions->dragAndDrop($this->proxyWebElement, $target);

        return $this;
    }

    public function dragAndDropBy($x_offset, $y_offset) : ProxyWebElementActions
    {
        $this->webDriverActions->dragAndDropBy($this->proxyWebElement, $x_offset, $y_offset);

        return $this;
    }

    public function moveByOffset($x_offset, $y_offset) : ProxyWebElementActions
    {
        $this->webDriverActions->moveByOffset($x_offset, $y_offset);

        return $this;
    }

    public function moveOnto($x_offset = null, $y_offset = null) : ProxyWebElementActions
    {
        $this->webDriverActions->moveToElement($this->proxyWebElement, $x_offset, $y_offset);

        return $this;
    }

    public function moveToElement(ProxyWebElement $element, $x_offset = null, $y_offset = null) : ProxyWebElementActions
    {
        $this->webDriverActions->moveToElement($element, $x_offset, $y_offset);

        return $this;
    }

    public function release() : ProxyWebElementActions
    {
        $this->webDriverActions->release($this->proxyWebElement);

        return $this;
    }

    public function keyDown($key = null) : ProxyWebElementActions
    {
        $this->webDriverActions->keyDown($this->proxyWebElement, $key);

        return $this;
    }

    public function keyUp($key = null) : ProxyWebElementActions
    {
        $this->webDriverActions->keyUp($this->proxyWebElement, $key);

        return $this;
    }

    public function sendKeys($keys = null) : ProxyWebElementActions
    {
        $this->webDriverActions->sendKeys($this->proxyWebElement, $keys);

        return $this;
    }
}
