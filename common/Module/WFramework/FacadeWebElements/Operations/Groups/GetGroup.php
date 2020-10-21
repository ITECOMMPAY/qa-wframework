<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 11:19
 */

namespace Common\Module\WFramework\FacadeWebElements\Operations\Groups;


use Common\Module\WFramework\FacadeWebElements\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use function implode;

class GetGroup extends OperationsGroup
{
    public function texts() : array
    {
        WLogger::logDebug('Получаем видимые строки элементов коллекции');

        $result = [];

        foreach ($this->facadeWebElements->getElementsArray() as $facadeWebElement)
        {
            $result[] = $facadeWebElement
                                        ->get()
                                        ->text()
                                        ;
        }

        WLogger::logDebug('Получили видимые строки: ' . implode(', ', $result));

        return $result;
    }

    public function rawTexts() : array
    {
        WLogger::logDebug('Получаем все строки (включая невидимые) элементов коллекции');

        $result = [];

        foreach ($this->facadeWebElements->getElementsArray() as $facadeWebElement)
        {
            $result[] = $facadeWebElement
                                        ->get()
                                        ->rawText()
                                        ;
        }

        WLogger::logDebug('Получили строки: ' . implode(', ', $result));

        return $result;
    }

    public function attributes(string $attribute) : array
    {
        WLogger::logDebug('Получаем атрибуты элементов коллекции');

        $result = array();

        foreach ($this->facadeWebElements->getElementsArray() as $facadeWebElement)
        {
            $result[] = $facadeWebElement
                ->get()
                ->attribute($attribute)
            ;
        }

        WLogger::logDebug('Получили атрибуты: ' . implode(', ', $result));

        return $result;
    }

    public function values() : array
    {
        WLogger::logDebug('Получаем значения элементов коллекции');

        $result = array();

        foreach ($this->facadeWebElements->getElementsArray() as $facadeWebElement)
        {
            $result[] = $facadeWebElement
                ->get()
                ->value()
            ;
        }

        WLogger::logDebug('Получили значения: ' . implode(', ', $result));

        return $result;
    }

}
