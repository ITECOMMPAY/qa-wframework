<?php


namespace Codeception\Lib\WFramework\WebObjects\Base\Interfaces;


interface IHaveCurrentValue
{
    /**
     * Возвращает текущее значение элемента в виде СТРОКИ.
     *
     * @return string
     */
    public function getCurrentValueString() : string;
}
