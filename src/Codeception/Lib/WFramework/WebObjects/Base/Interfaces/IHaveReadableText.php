<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 05.03.19
 * Time: 13:34
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\Interfaces;


interface IHaveReadableText
{
    /**
     * Возвращает весь текст элемента (включая невидимый)
     */
    public function getAllText() : string;

    /**
     * Возвращает видимый текст элемента
     */
    public function getVisibleText() : string;

    /**
     * Возвращает видимый текст элемента, отфильтрованный по регулярке
     */
    public function getFilteredText(string $regex, string $groupName = "") : string;
}
