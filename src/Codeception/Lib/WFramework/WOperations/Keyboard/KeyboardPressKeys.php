<?php


namespace Codeception\Lib\WFramework\WOperations\Keyboard;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class KeyboardPressKeys extends AbstractOperation
{
    /**
     * @var mixed
     */
    protected $keys;

    /**
     * Посылает символы элементу.
     *
     * Для эмуляции нажатия комбинации специальных клавиш вместе с обычными - следует использовать массив.
     * Специальные клавиши содержаться в классе WebDriverKeys.
     *
     * Например:
     *
     *      $element
     *             ->returnOperations()
     *             ->keyboard()
     *             ->pressKeys([WebDriverKeys::CONTROL, WebDriverKeys::END])
     *             ;
     *
     * @param mixed $keys - символы, которые нужно послать элементу
     */
    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    public function acceptWCollection($collection)
    {
        $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject)
    {
        WLogger::logDebug('Посылаем элементу символы: ' . json_encode($this->keys));

        $pageObject
            ->getProxyWebElement()
            ->sendKeys($this->keys)
            ;
    }
}
