<?php


namespace Codeception\Lib\WFramework\WOperations\Execute;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class ExecuteScript extends AbstractOperation
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
     * Выполняет JavaScript в браузере.
     *
     * Для выполнения скрипта с данным элементом в качестве аргумента - используйте метод scriptOnThis().
     *
     * @param string $script - строка, содержащая код на языке JavaScript
     * @param array $arguments - массив аргументов. В запускаемом JavaScript они будут лежать в
     *                           массиве 'arguments': arguments[0], arguments[1] и т.д.
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

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Выполняем скрипт: ' . $this->script);

        return $pageObject
                    ->getProxyWebElement()
                    ->executeScript($this->script, $this->arguments)
                    ;
    }
}
