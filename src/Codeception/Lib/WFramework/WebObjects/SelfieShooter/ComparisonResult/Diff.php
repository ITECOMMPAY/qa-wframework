<?php


namespace Codeception\Lib\WFramework\WebObjects\SelfieShooter\ComparisonResult;


class Diff implements IComparisonResult
{
    /** @var int */
    public $deviation;

    /** @var string */
    public $diffImage;

    public function __construct(int $deviation, string $diffImage)
    {
        $this->deviation = $deviation;
        $this->diffImage = $diffImage;
    }
}
