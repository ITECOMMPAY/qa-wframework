<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Exceptions\VisitorNotImplementedException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
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

        foreach ($this->getParentAcceptMethods($pageObject) as $methodToCall)
        {
            if (method_exists($this, $methodToCall))
            {
                return $this->$methodToCall($pageObject);
            }
        }

        $poClassFull = $pageObject->getClass();
        $poClassShort = $pageObject->getClassShort();

        throw new VisitorNotImplementedException( "Визитор: " . static::class . " - не умеет работать с $poClassFull. Если это необходимо, реализуйте в визиторе метод 'accept$poClassShort' или более общий - '$methodToCall'");
    }

    public function applicable(IPageObject $pageObject) : bool
    {
        WLogger::logDebug($this, 'Применимо ли к: ' . $pageObject . ' - условие: ' . $this);

        if (method_exists($this, 'accept' . $pageObject->getClassShort()))
        {
            return true;
        }

        foreach ($this->getParentAcceptMethods($pageObject) as $methodToCall)
        {
            if (method_exists($this, $methodToCall))
            {
                return true;
            }
        }

        return false;
    }

    private function getParentAcceptMethods(IPageObject $pageObject) : array
    {
        $result = [];

        foreach (class_parents($pageObject) as $class)
        {
            if ($class === WPageObject::class)
            {
                break;
            }

            $result[] = 'accept' . ClassHelper::getShortName($class);
        }

        return $result;
    }

    public function __toString() : string
    {
        return $this->getName();
    }
}
