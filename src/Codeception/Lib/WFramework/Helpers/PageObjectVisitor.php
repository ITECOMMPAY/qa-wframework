<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

/**
 * Class PageObjectVisitor
 *
 * @method mixed acceptWElement(WElement $element)
 * @method mixed acceptWBlock(WBlock $block)
 * @method mixed acceptWCollection(WCollection $collection)
 * @package Codeception\Lib\WFramework\Helpers
 */
abstract class PageObjectVisitor extends CompositeVisitor
{
    /**
     * Понятное описание визитора.
     *
     * Нужно потому что из названия класса не всегда понятно - что этот визитор должен делать, а так же для логов.
     *
     * Писать нужно из соображения, что в логах оно должно отображаться в следующем формате:
     *      Элемент "Кнопка логина" -> виден?
     *      Блок "Форма логина" -> делаем скриншот
     *
     * @return string
     */
    abstract public function getName() : string;

    public function __toString() : string
    {
        return $this->getName();
    }

    protected function shouldStopAfterClass(string $fullClassName) : bool
    {
        return WPageObject::class === $fullClassName;
    }
}
