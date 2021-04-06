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
use Imagick;
use ImagickPixel;

class GetHistogram extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем гистограмму цветов";
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
     * Возвращает гистограмму для элемента
     *
     * @param \Codeception\Lib\WFramework\Helpers\Color|null $ignoredColor - не учитывать заданный цвет
     * @param string|null $screenshot - если скриншот не передан в этом параметре, то метод сам его снимет
     */
    public function __construct(?Color $ignoredColor = null, ?string $screenshot = null)
    {
        $this->ignoredColor = $ignoredColor;
        $this->screenshot = $screenshot;
    }

    public function acceptWBlock($block) : array
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : array
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

    protected function apply(WPageObject $pageObject) : array
    {
        if ($this->screenshot === null)
        {
            $this->screenshot = $pageObject->accept(new GetScreenshotRaw());
        }

        $imagick = new Imagick();
        $imagick->readImageBlob($this->screenshot);
        $histogram = $imagick->getImageHistogram();

        $stat = [];

        /** @var ImagickPixel $histogramElement */
        foreach ($histogram as $histogramElement)
        {
            $color = Color::fromImagickColor($histogramElement->getColor());

            if ($this->ignoredColor !== null && $color->equals($this->ignoredColor))
            {
                continue;
            }

            $stat[$histogramElement->getColorCount()] = $color;
        }

        return $stat;
    }
}
