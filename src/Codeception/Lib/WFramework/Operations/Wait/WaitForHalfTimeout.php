<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


class WaitForHalfTimeout extends AbstractWaitForTimeout
{
    public function getName() : string
    {
        return 'Ждём половину от заданного таймаута';
    }

    protected function getDivisor() : int
    {
        return 2;
    }
}
