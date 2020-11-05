<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:20
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Common\Module\WFramework\Properties\TestProperties;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class Visible extends Cond
{
    static $isSafari;

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        try
        {
            if (!$facadeWebElement->returnProxyWebElement()->isExist())
            {
                $this->result = false;
                return;
            }

            if ($this->isSafari())
            {
                // Safari пока не поддерживает /session/{}/element/{}/displayed
                $this->result = (bool) $facadeWebElement->exec()->scriptOnThis(static::SCRIPT_VISIBLE);
                return;
            }

            $this->result = $facadeWebElement->mouse()->scrollTo()->then()->returnProxyWebElement()->isDisplayed() ?? false;
        }
        catch (NoSuchElementException $e)
        {
            $this->result = False;
        }
        catch (StaleElementReferenceException $e)
        {
            $this->result = False;
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
        return "должен быть виден";
    }

    public function printActualValue() : string
    {
        return $this->result ? 'виден' : 'не виден';
    }

    //https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js
    protected const SCRIPT_VISIBLE  = <<<EOF
return !!( arguments[0].offsetWidth || arguments[0].offsetHeight || Array.from(arguments[0].getClientRects()).filter(function(el){return el.width > 0 && el.height > 0;}).length );
EOF;

}
