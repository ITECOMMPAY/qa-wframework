<?php


namespace Codeception\Lib\WFramework\WOperations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class MouseClickHoldAndMove extends AbstractOperation
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
     * Зажимает ЛКМ на элементе и двигает курсор на X, Y; затем отпускает;
     * фактически, драг-н-дроп
     *
     * @param int $offsetX - смещение по оси X
     * @param int $offsetY - смещение по оси Y
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

    public function apply(WPageObject $pageObject)
    {
        WLogger::logDebug("Зажимаем ЛКМ на элементе и двигаем по оффсетам: X$this->offsetX, Y$this->offsetY");

        $pageObject
            ->getProxyWebElement()
            ->executeActions()
            ->dragAndDropBy($this->offsetX, $this->offsetY)
            ->perform();
    }
}
