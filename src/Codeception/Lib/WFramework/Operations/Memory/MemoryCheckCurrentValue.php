<?php


namespace Codeception\Lib\WFramework\Operations\Memory;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Ds\Sequence;

class MemoryCheckCurrentValue extends AbstractOperation
{
    public function getName() : string
    {
        return "сравниваем текущее значение с запомненным";
    }

    /**
     * @var string
     */
    protected $customKey;

    /**
     * Сравнивает текущее значение PageObject'а с запомненным. Для этого PageObject должен реализовывать интерфейс IHaveCurrentValue.
     *
     * Значение будет запомнено в TestProperties по ключу равному имени PageObject'а или по заданному $customKey.
     *
     * @param string $customKey
     */
    public function __construct(string $customKey = '')
    {
        $this->customKey = $customKey;
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    /**
     * @param WCollection $collection
     *
     * @return \Ds\Sequence - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : Sequence
    {
        return $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        if (!$pageObject instanceof IHaveCurrentValue)
        {
            throw new UsageException($pageObject . ' -> чтобы получить возможность запоминать текущее значение объект должен реализовывать интерфейс IHaveCurrentValue');
        }

        $key = empty($this->customKey) ? (string) $pageObject : $this->customKey;

        $expectedValue = TestProperties::mustGetValue($key);

        $actualValue = $pageObject->getCurrentValueString();

        WLogger::logDebug($this, 'Ожидаемое значение: ' . $expectedValue . PHP_EOL . ' - актуальное значение: ' . $actualValue);

        return $expectedValue === $actualValue;
    }
}
