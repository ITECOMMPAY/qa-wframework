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
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class Visible extends Cond
{
    protected function apply(FacadeWebElement $facadeWebElement)
    {
        try
        {
            if (!$facadeWebElement->returnProxyWebElement()->isExist())
            {
                $this->result = False;
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

    public function printExpectedValue() : string
    {
        return "должен быть виден";
    }

    public function printActualValue() : string
    {
        return $this->result ? 'виден' : 'не виден';
    }
}
