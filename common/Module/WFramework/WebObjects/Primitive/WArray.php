<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.03.19
 * Time: 17:27
 */

namespace Common\Module\WFramework\WebObjects\Primitive;


use Common\Module\WFramework\Exceptions\WArray\EmptyException;
use Common\Module\WFramework\Exceptions\WArray\NoSuchIndexException;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;
use Common\Module\WFramework\WebObjects\Base\WElements\WElements;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IIndexedCollection;

class WArray extends WElements implements IIndexedCollection
{
    public function get(int $index) : WElement
    {
        WLogger::logInfo($this . " -> получаем элемент по индексу: $index");

        $elementsArray = $this->getElementsArray();

        if (empty($elementsArray))
        {
            throw new EmptyException($this . ' - не содержит элементов.');
        }

        if (!isset($elementsArray[$index]))
        {
            throw new NoSuchIndexException($this . ' - не содержит элементов.');
        }

        return $elementsArray[$index];
    }

    public function hasIndex(int $index) : bool
    {
        WLogger::logInfo($this . " -> есть элемент по индексу: $index?");

        $elementsArray = $this->getElementsArray();

        return isset($elementsArray[$index]);
    }
}
