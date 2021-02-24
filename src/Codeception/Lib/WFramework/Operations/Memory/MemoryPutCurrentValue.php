<?php


namespace Codeception\Lib\WFramework\Operations\Memory;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;

class MemoryPutCurrentValue extends AbstractOperation
{
    /**
     * @var string
     */
    protected $customKey;

    public function getName() : string
    {
        return "запоминаем текущее значение";
    }

    /**
     * Запоминает текущее значение PageObject'а. Для этого PageObject должен реализовывать интерфейс IHaveCurrentValue.
     *
     * Значение будет запомнено в TestProperties по ключу равному имени PageObject'а или по заданному $customKey.
     *
     * @param string $customKey
     */
    public function __construct(string $customKey = '')
    {
        $this->customKey = $customKey;
    }

    public function acceptWBlock($block)
    {
        $this->apply($block);
    }

    public function acceptWElement($element)
    {
        $this->apply($element);
    }

    /**
     * @param WCollection $collection
     */
    public function acceptWCollection($collection)
    {
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        if (!$pageObject instanceof IHaveCurrentValue)
        {
            throw new UsageException($pageObject . ' -> чтобы получить возможность запоминать текущее значение объект должен реализовывать интерфейс IHaveCurrentValue');
        }

        $key = empty($this->customKey) ? (string) $pageObject : $this->customKey;

        TestProperties::setValue($key, $pageObject->getCurrentValueString());
    }
}
