<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Exceptions\VisitorNotImplementedException;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

/**
 * Class PageObjectVisitor
 *
 * @method mixed acceptWElement($element)
 * @method mixed acceptWBlock($block)
 * @method mixed acceptWCollection($collection)
 * @package Codeception\Lib\WFramework\Helpers
 */
abstract class PageObjectVisitor
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

    public function __call(string $name, array $arguments)
    {
        $pageObject = reset($arguments);

        if (!$pageObject instanceof IPageObject)
        {
            throw new UsageException('Первым аргументов визитора должен быть IPageObject');
        }

        $methodToCall = $this->getDefaultMethod($pageObject);

        if (!method_exists($this, $methodToCall))
        {
            $poClassFull = $pageObject->getClass();
            $poClassShort = $pageObject->getClassShort();

            throw new VisitorNotImplementedException( "Визитор: " . static::class . " - не умеет работать с $poClassFull. Если это необходимо, реализуйте в визиторе метод 'accept$poClassShort' или более общий - '$methodToCall'");
        }

        return $this->$methodToCall($pageObject);
    }

    private function getDefaultMethod(IPageObject $pageObject) : string
    {
        if ($pageObject instanceof WElement)
        {
            return 'acceptWElement';
        }

        if ($pageObject instanceof WBlock)
        {
            return 'acceptWBlock';
        }

        if ($pageObject instanceof WCollection)
        {
            return 'acceptWCollection';
        }

        throw new UsageException('PageObject должен быть наследником WBlock, WElement или WCollection');
    }

    public function applicable(IPageObject $pageObject) : bool
    {
        if (method_exists($this, 'accept' . $pageObject->getClassShort()))
        {
            return true;
        }

        return method_exists($this, $this->getDefaultMethod($pageObject));
    }

    public function __toString() : string
    {
        return $this->getName();
    }
}
