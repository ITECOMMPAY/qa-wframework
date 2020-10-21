<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 13:49
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;


class Hidden extends Cond
{

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        try
        {
            if (!$facadeWebElement->returnProxyWebElement()->isExist())
            {
                $this->result = true;
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

    public function printExpectedValue() : string
    {
        return 'не должен отображаться';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'не отображается' : 'отображается' ;
    }
}
