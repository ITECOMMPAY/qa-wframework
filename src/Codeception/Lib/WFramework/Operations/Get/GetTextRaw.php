<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Sequence;

class GetTextRaw extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем сырой текст (включая невидимый)";
    }

    /**
     * Получает сырой текст элемента (включая невидимый)
     */
    public function __construct() { }

    public function acceptWBlock($block) : string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : string
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

    protected function apply(WPageObject $pageObject) : string
    {
        return $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_GET_TEXT));
    }

    const SCRIPT_GET_TEXT = <<<EOF
let content = arguments[0].textContent;

if (content === "")
{
    content = arguments[0].getAttribute('value') ?? '';
}

return content;
EOF;
}
