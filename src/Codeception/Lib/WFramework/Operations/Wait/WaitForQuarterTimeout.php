<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


class WaitForQuarterTimeout extends AbstractWaitForTimeout
{
    public function getName() : string
    {
        return 'Ждём четверть от заданного таймаута';
    }

    protected function getDivisor() : int
    {
        return 4;
    }
}
