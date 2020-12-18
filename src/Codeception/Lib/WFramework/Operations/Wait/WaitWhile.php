<?php


namespace Codeception\Lib\WFramework\Operations\Wait;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Exceptions\WaitUntilElement;
use Codeception\Lib\WFramework\Exceptions\WaitWhileElement;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;

class WaitWhile extends AbstractOperation
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    public function getName() : string
    {
        return "ждём выполнения условия: " . $this->condition;
    }

    /**
     * Ожидает, пока для данного PageObject'а начнут выполняться условия,
     * или не пройдёт, заданный в настройках модуля, elementTimeout / collectionTimeout.
     *
     * @param AbstractCondition $condition - условие
     * @throws WaitUntilElement - не удалось дождаться выполнения условий для данного элемента
     */
    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
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
        $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject)
    {
        $deadLine = microtime(True) + $pageObject->getTimeout();

        while (microtime(True) < $deadLine)
        {
            if ($pageObject instanceof WCollection)
            {
                $pageObject->refresh();
            }

            if ($pageObject->accept($this->condition))
            {
                return $this;
            }

            usleep(500000);
        }

        throw new WaitWhileElement('Не удалось дождаться выполнения условия: ' . $this->condition);
    }
}
