<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.03.19
 * Time: 17:27
 */

namespace Common\Module\WFramework\WebObjects\Primitive\Interfaces;


interface IIndexedCollection
{
    /**
     * Возвращает элемент коллекции по заданному смещению
     */
    public function get(int $index);

    /**
     * Имеет элемент по смещению?
     */
    public function hasIndex(int $index) : bool;
}
