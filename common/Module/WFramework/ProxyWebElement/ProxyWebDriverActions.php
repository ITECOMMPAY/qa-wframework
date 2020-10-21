<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 11:46
 */

namespace Common\Module\WFramework\ProxyWebElement;


use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverHasInputDevices;

class ProxyWebDriverActions
{
    protected $webDriverActions;

    protected $webElementProxy;
    
    public function __construct(WebDriverActions $webDriverActions, ProxyWebElement $webElementProxy)
    {
        $this->webDriverActions = $webDriverActions;
        
        $this->webElementProxy = $webElementProxy;
    }
    
    //------------------------------------------------------------------------------------------------------------------
    public function perform()
    {
        $this->webDriverActions->perform(); 
    }

    public function click() : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->click($this->webElementProxy);

        return $this;
    }

    public function clickAndHold() : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->clickAndHold($this->webElementProxy);

        return $this;
    }

    public function contextClick() : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->contextClick($this->webElementProxy);

        return $this;
    }

    public function doubleClick() : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->doubleClick($this->webElementProxy);

        return $this;
    }

    public function dragAndDrop(WebDriverElement $target) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->dragAndDrop($this->webElementProxy, $target);

        return $this;
    }

    public function dragAndDropBy($x_offset, $y_offset) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->dragAndDropBy($this->webElementProxy, $x_offset, $y_offset);

        return $this;
    }

    public function moveByOffset($x_offset, $y_offset) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->moveByOffset($x_offset, $y_offset);

        return $this;
    }

    public function moveToElement($x_offset = null, $y_offset = null) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->moveToElement($this->webElementProxy, $x_offset, $y_offset);

        return $this;
    }

    public function release() : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->release($this->webElementProxy);

        return $this;
    }

    public function keyDown($key = null) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->keyDown($this->webElementProxy, $key);

        return $this;
    }

    public function keyUp($key = null) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->keyUp($this->webElementProxy, $key);

        return $this;
    }

    public function sendKeys($keys = null) : ProxyWebDriverActions
    {
        $this->webDriverActions = $this->webDriverActions->sendKeys($this->webElementProxy, $keys);

        return $this;
    }


}
