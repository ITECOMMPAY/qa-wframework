<?php


namespace Codeception\Lib\WFramework\WebObjects\Base;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WSomeElement extends WElement
{
    protected function initTypeName() : string
    {
        return "Некий элемент";
    }
}