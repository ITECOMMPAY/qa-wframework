<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:49
 */

namespace Codeception\Lib\WFramework\Condition\Operator;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;


class Hidden extends Cond
{
    static $isSafari;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        try
        {
            if (!$facadeWebElement->returnProxyWebElement()->isExist())
            {
                $this->result = true;
                return;
            }

            if ($this->isSafari())
            {
                // Safari пока не поддерживает /session/{}/element/{}/displayed
                $this->result = ! (bool) $facadeWebElement->exec()->scriptOnThis(static::SCRIPT_VISIBLE);
                return;
            }

            $displayed = $facadeWebElement->returnProxyWebElement()->isDisplayed() ?? false;
            $this->result = !$displayed;
        }
        catch (NoSuchElementException $e)
        {
            $this->result = true;
        }
        catch (StaleElementReferenceException $e)
        {
            $this->result = true;
        }
    }

    protected function isSafari() : bool
    {
        if (!isset(static::$isSafari))
        {
            static::$isSafari = strtolower(TestProperties::getValue('browser', '')) === 'safari';
        }

        return static::$isSafari;
    }

    public function printExpectedValue() : string
    {
        return 'не должен отображаться';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'не отображается' : 'отображается' ;
    }

    //https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js
    protected const SCRIPT_VISIBLE  = <<<EOF
return !!( arguments[0].offsetWidth || arguments[0].offsetHeight || Array.from(arguments[0].getClientRects()).filter(function(el){return el.width > 0 && el.height > 0;}).length );
EOF;
}
