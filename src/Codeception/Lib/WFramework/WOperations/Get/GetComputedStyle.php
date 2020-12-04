<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class GetComputedStyle extends AbstractOperation
{
    /**
     * @var string|null
     */
    protected $pseudoElement;

    /**
     * Возвращает массив стилей элемента
     *
     * @param string|null $pseudoElement - CSS псевдо-элемент (::before, ::after, ::selection и т.д.)
     *                                     для которого нужно получить массив стилей
     */
    public function __construct(?string $pseudoElement = null)
    {
        $this->pseudoElement = $pseudoElement;
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
     * @return array - массив результатов применения операции для каждого элемента коллекции
     */
    public function acceptWCollection($collection) : array
    {
        return $this->applyToEveryElement([$this, 'apply'], $collection);
    }

    protected function apply(WPageObject $pageObject) : array
    {
        WLogger::logDebug('Получаем массив стилей элемента');

        $element = $pageObject->getProxyWebElement();

        $computedStyle = $element->executeScriptOnThis(static::SCRIPT_GET_COMPUTED_STYLE, [$this->pseudoElement]);

        $result = [];

        foreach ($computedStyle as $entry)
        {
            [$key, $value] = $entry;

            if (is_numeric($key))
            {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    protected const SCRIPT_GET_COMPUTED_STYLE = <<<EOF

var result = [];

var obj = window.getComputedStyle(arguments[0], arguments[1]);

for(var key in obj) {
    var value = obj[key];
    result.push([key, value]);
}

return result;
EOF;
}
