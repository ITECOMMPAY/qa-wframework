<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Helpers\Color;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetSecondaryColor extends AbstractOperation
{
    /**
     * @var Color|null
     */
    protected $ignoredColor;

    /**
     * @var string|null
     */
    protected $screenshot;

    /**
     * Возвращает второстепенный цвет элемента.
     *
     * @param Color|null $ignoredColor - при определении основного цвета не учитывать заданный цвет
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : Color
    {
        WLogger::logDebug('Получаем второстепенный цвет элемента');

        if ($this->screenshot === null)
        {
            $this->screenshot = $pageObject->accept(new GetScreenshot());
        }

        $stat = $pageObject->accept(new GetHistogram($this->ignoredColor, $this->screenshot));

        krsort($stat);

        $keys = array_keys($stat);
        $key = $keys[1] ?? $keys[0];

        $secondaryColor = $stat[$key];

        WLogger::logDebug('Получили второстепенный цвет: ' . $secondaryColor);

        return $secondaryColor;
    }
}
