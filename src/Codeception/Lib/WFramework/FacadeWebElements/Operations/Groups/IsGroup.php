<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 12:58
 */

namespace Codeception\Lib\WFramework\FacadeWebElements\Operations\Groups;

use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\FacadeWebElements\Operations\OperationsGroup;
use Codeception\Lib\WFramework\Logger\WLogger;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;


class IsGroup extends OperationsGroup
{
    public function is(CCond ...$conditions) : bool
    {
        WLogger::logDebug('Проверяем, что для каждого элемента коллекции выполняются заданные условия');

        foreach ($conditions as $cond)
        {
            if (!$this->checkCondition($cond))
            {
                return False;
            }
        }

        return True;
    }

    public function isNot(CCond ...$conditions) : bool
    {
        WLogger::logDebug('Проверяем, что для каждого элемента коллекции НЕ выполняются заданные условия');

        foreach ($conditions as $cond)
        {
            if (!$this->checkCondition($cond, true))
            {
                return False;
            }
        }

        return True;
    }

    public function has(CCond ...$conditions) : bool
    {
        return $this->is(...$conditions);
    }

    public function doesNotHave(CCond ...$conditions) : bool
    {
        return $this->isNot(...$conditions);
    }

    protected function checkCondition(CCond $cond, bool $invert = false) : bool
    {
        $check = $invert ? CCond::not($cond) : $cond;

        $passed = True;

        try
        {
            if ($check->check($this->facadeWebElements) === False)
            {
                $passed = False;
            }
        }
        catch (NoSuchElementException $e)
        {
            $passed = False;
        }
        catch (StaleElementReferenceException $e)
        {
            $passed = False;
        }

        return $passed;
    }
}
