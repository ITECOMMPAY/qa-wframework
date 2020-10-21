<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 05.03.19
 * Time: 13:31
 */

namespace Common\Module\WFramework\WebObjects\Primitive\Interfaces;


interface IHaveWritableText
{
    /**
     * Задаёт текст элемента
     */
    public function set(string $text);

    /**
     * Добавляет текст в элемент
     */
    public function append(string $text);

    /**
     * Очищает текст элемента
     */
    public function clear();
}
