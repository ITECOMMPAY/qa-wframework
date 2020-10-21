<?php


namespace Common\Module\WFramework\WebObjects\Primitive\Interfaces;


interface IHaveCurrentValue
{
    /**
     * Возвращает текущее значение элемента в виде СТРОКИ.
     *
     * @return string
     */
    public function getCurrentValueString() : string;
}
