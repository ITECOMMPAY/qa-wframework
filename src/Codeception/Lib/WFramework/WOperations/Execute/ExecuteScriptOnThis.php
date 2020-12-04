<?php


namespace Codeception\Lib\WFramework\WOperations\Execute;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class ExecuteScriptOnThis extends AbstractOperation
{
    /**
     * @var string
     */
    protected $script;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * Выполняет JavaScript с данным элементом в качестве аргумента (arguments[0]).
     *
     * Пример:
     *
     *     $element->executeScriptOnThis("arguments[0].scrollIntoView(true);"); // - прокручиваем окно к данному элементу
     *
     * @param string $script - строка, содержащая код на языке JavaScript
     * @param array $arguments - массив аргументов. В запускаемом JavaScript они будут лежать в
     *                           массиве 'arguments', и будут начинаться с индекса 1, т.к. по нулевому индексу
     *                           лежит данный элемент: arguments[1], arguments[2] и т.д.
     */
    public function __construct(string $script, array $arguments = [])
    {
        $this->script = $script;
        $this->arguments = $arguments;
    }

    /**
     * @param WBlock $block
     * @return mixed - если скрипт возвращает значение, то значение скрипта.
     */
    public function acceptWBlock($block)
    {
        return $this->apply($block);
    }

    /**
     * @param WElement $element
     * @return mixed - если скрипт возвращает значение, то значение скрипта.
     */
    public function acceptWElement($element)
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return array - если скрипт возвращает значение, то массив значений скрипта.
     */
    public function acceptWCollection($collection)
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Выполняем скрипт для элемента: ' . $this->script);

        return $pageObject
                    ->getProxyWebElement()
                    ->executeScriptOnThis($this->script, $this->arguments)
                    ;
    }
}
