<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.03.19
 * Time: 17:27
 */

namespace Codeception\Lib\WFramework\WebObjects\Primitive;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Primitive\Interfaces\IIndexedCollection;

class WArray extends WCollection implements IIndexedCollection
{
    public function get(int $index) : WElement
    {
        WLogger::logInfo($this . " -> получаем элемент по индексу: $index");

        $elementsArray = $this->getElementsArray();

        if (empty($elementsArray))
        {
            throw new UsageException($this . ' - не содержит элементов.');
        }

        if (!isset($elementsArray[$index]))
        {
            throw new UsageException($this . ' - не содержит элементов.');
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
