<?php


namespace Codeception\Lib\WFramework\Conditions\Interfaces;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;

interface IWrapOtherCondition
{
    public function getWrappedCondition() : AbstractCondition;
}