<?php


namespace Codeception\Lib\WFramework\Operations\Execute;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class ExecuteScriptOnThis extends AbstractOperation
{
    public function getName() : string
    {
        return "выполняем скрипт для данного PageObject'а: '" . substr($this->script, 0, 64) . "' c аргументами: " . implode(', ', $this->arguments);
    }

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
     * @return \Ds\Sequence - если скрипт возвращает значение, то массив значений скрипта.
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        return $pageObject
                    ->should(new Exist())
                    ->returnSeleniumElement()
                    ->executeScriptOnThis($this->script, $this->arguments)
                    ;
    }
}
