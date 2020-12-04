<?php


namespace Codeception\Lib\WFramework\WOperations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class MouseClickWithRightButton extends AbstractOperation
{
    /**
     * @var int
     */
    protected $offsetX;
    /**
     * @var int
     */
    protected $offsetY;

    /**
     * Осуществляет клик правой кнопкой мыши на данном элементе с помощью Selenium Actions
     *
     * @param int $offsetX - опциональное смещение от центра элемента по оси X
     * @param int $offsetY - опциональное смещение от центра элемента по оси Y
     */
    public function __construct(int $offsetX = 0, int $offsetY = 0)
    {
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
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

    public function apply(WPageObject $pageObject)
    {
        WLogger::logDebug("Кликаем правой кнопкой мыши на элементе, смещение от центра: X$this->offsetX, Y$this->offsetY");

        $pageObject
            ->getProxyWebElement()
            ->executeActions()
            ->moveToElement($this->offsetX, $this->offsetY)
            ->contextClick()
            ->perform()
            ;
    }
}
