<?php


namespace Codeception\Lib\WFramework\Operations\Keyboard;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class KeyboardPressKeys extends AbstractOperation
{
    public function getName() : string
    {
        return "посылаем символы: " . json_encode($this->keys);
    }

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
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        $pageObject
            ->should(new Exist())
            ->returnSeleniumElement()
            ->sendKeys($this->keys)
            ;
    }
}
