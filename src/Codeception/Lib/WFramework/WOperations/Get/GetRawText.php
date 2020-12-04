<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetRawText extends AbstractOperation
{
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : string
    {
        WLogger::logDebug('Получаем сырой текст элемента (включая невидимый)');

        $result = $pageObject
                        ->getProxyWebElement()
                        ->executeScriptOnThis(static::SCRIPT_GET_TEXT)
                        ;

        WLogger::logDebug('Получили сырой текст: ' . $result);

        return $result;
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
