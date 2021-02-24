<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


class WaitForEighthTimeout extends AbstractWaitForTimeout
{
    public function getName() : string
    {
        return 'Ждём восьмую часть от заданного таймаута';
    }

    protected function getDivisor() : int
    {
        return 8;
    }
}
