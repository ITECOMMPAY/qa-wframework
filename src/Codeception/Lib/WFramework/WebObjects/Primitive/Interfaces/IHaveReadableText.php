<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 05.03.19
 * Time: 13:34
 */

namespace Codeception\Lib\WFramework\WebObjects\Primitive\Interfaces;


interface IHaveReadableText
{
    /**
     * Возвращает видимый текст элемента
     */
    public function getVisibleText() : string;

    /**
     * Возвращает видимый текст элемента, отфильтрованный по регулярке
     */
    public function getFilteredText(string $regex) : string;
}
