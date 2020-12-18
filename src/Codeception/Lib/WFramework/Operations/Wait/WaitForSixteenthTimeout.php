<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


class WaitForSixteenthTimeout extends AbstractWaitForTimeout
{
    public function getName() : string
    {
        return 'Ждём шестнадцатую часть от заданного таймаута';
    }

    protected function getDivisor() : int
    {
        return 16;
    }
}
