<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Color;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetPrimaryColor extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем основной цвет";
    }

    /**
     * @var Color|null
     */
    protected $ignoredColor;

    /**
     * @var string|null
     */
    protected $screenshot;

    /**
     * Возвращает основной цвет элемента.
     *
     * @param \Codeception\Lib\WFramework\Helpers\Color|null $ignoredColor - при определении основного цвета не учитывать заданный цвет
     * @param string|null $screenshot - если скриншот не передан в этом параметре, то метод сам его снимет
     */
    public function __construct(?Color $ignoredColor = null, ?string $screenshot = null)
    {
        $this->ignoredColor = $ignoredColor;
        $this->screenshot = $screenshot;
    }

    public function acceptWBlock($block) : Color
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Color
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : Color
    {
        if ($this->screenshot === null)
        {
            $this->screenshot = $pageObject->accept(new GetScreenshotRaw());
        }

        $stat = $pageObject->accept(new GetHistogram($this->ignoredColor, $this->screenshot));

        krsort($stat);

        $primaryColor = reset($stat);

        WLogger::logDebug($this, 'Получили основной цвет: ' . $primaryColor);

        return $primaryColor;
    }
}
