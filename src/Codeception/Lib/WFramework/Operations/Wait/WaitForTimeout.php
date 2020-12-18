<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


class WaitForTimeout extends AbstractWaitForTimeout
{
    public function getName() : string
    {
        return 'Ждём заданный таймаут';
    }

    protected function getDivisor() : int
    {
        return 1;
    }
}
