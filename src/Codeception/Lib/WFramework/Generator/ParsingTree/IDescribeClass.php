<?php


namespace Codeception\Lib\WFramework\Generator\ParsingTree;


interface IDescribeClass
{
    public function getEntityClassShort() : string;

    public function getEntityClassFull() : string;

    public function getOutputNamespace() : string;
}