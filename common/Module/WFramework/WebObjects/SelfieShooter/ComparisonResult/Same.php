<?php


namespace Common\Module\WFramework\WebObjects\SelfieShooter\ComparisonResult;


class Same implements IComparisonResult
{
    /** @var int */
    public $deviation;

    public function __construct(int $deviation)
    {
        $this->deviation = $deviation;
    }
}
