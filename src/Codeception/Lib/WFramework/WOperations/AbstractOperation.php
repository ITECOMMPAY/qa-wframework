<?php


namespace Codeception\Lib\WFramework\WOperations;


use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

abstract class AbstractOperation
{
    /**
     * @param WElement $element
     * @throws UsageException
     * @return mixed|void
     */
    public function acceptWElement($element)
    {
        throw new UsageException( 'Визитор: ' . static::class . ' - не умеет работать с элементами. Реализуйте для него метод acceptWElement().');
    }

    /**
     * @param WBlock $block
     * @throws UsageException
     * @return mixed|void
     */
    public function acceptWBlock($block)
    {
        throw new UsageException( 'Визитор: ' . static::class . ' - не умеет работать с блоками. Реализуйте для него метод acceptWBlock().');
    }

    /**
     * @param WCollection $collection
     * @throws UsageException
     * @return mixed|void
     */
    public function acceptWCollection($collection)
    {
        throw new UsageException( 'Визитор: ' . static::class . ' - не умеет работать с коллекцией элементов. Реализуйте для него метод acceptWCollection().');
    }

    public function __call(string $name, array $arguments)
    {
        $pageObject = reset($arguments);

        if (!$pageObject instanceof IPageObject)
        {
            throw new UsageException('Первым аргументов визитора должен быть IPageObject');
        }

        if ($pageObject instanceof WElement)
        {
            $methodToCall = 'acceptWElement';
        }
        elseif ($pageObject instanceof WBlock)
        {
            $methodToCall = 'acceptWBlock';
        }
        elseif ($pageObject instanceof WCollection)
        {
            $methodToCall = 'acceptWCollection';
        }
        else
        {
            throw new UsageException('PageObject должен быть наследником WBlock, WElement или WCollection');
        }

        return $this->$methodToCall($pageObject);
    }

    protected function applyToEveryElement(callable $func, WCollection $collection) : array
    {
        $result = [];

        /** @var WElement $element */
        foreach ($collection->getChildren() as $name => $element)
        {
            $result[] = $func($element);
        }

        return $result;
    }
}
