<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Ds\Map;
use Ds\Sequence;

class GetComputedStyleMap extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем массив стилей";
    }

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

    public function acceptWBlock($block) : Map
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Map
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

    protected function apply(WPageObject $pageObject) : Map
    {
        $element = $pageObject->returnSeleniumElement();

        $computedStyles = $element->executeScriptOnThis(static::SCRIPT_GET_COMPUTED_STYLE, [$this->pseudoElement]);

        return new Map($computedStyles);
    }

    protected const SCRIPT_GET_COMPUTED_STYLE = <<<EOF
function getStyles(element, pseudoElement = null) {
    var result = {};

    let styles = window.getComputedStyle(element, pseudoElement);

    for (const [key, value] of Object.entries(styles)) {
        if (!isNaN(key)) {
            continue;
        }

        result[key] = value;
    }

    return result;
}

return getStyles(arguments[0], arguments[1]);
EOF;
}
