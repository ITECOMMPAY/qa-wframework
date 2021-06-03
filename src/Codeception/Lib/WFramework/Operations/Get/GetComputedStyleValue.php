<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Ds\Sequence;

class GetComputedStyleValue extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем стиль: $this->style" . isset($this->pseudoElement) ? " ($this->pseudoElement)" : '';
    }

    /**
     * @var string
     */
    protected $style;

    /**
     * @var string|null
     */
    protected $pseudoElement;

    /**
     * Возвращает стиль элемента
     *
     * @param string      $style
     * @param string|null $pseudoElement  - CSS псевдо-элемент (::before, ::after, ::selection и т.д.)
     *                                     для которого нужно получить массив стилей
     */
    public function __construct(string $style, ?string $pseudoElement = null)
    {
        $this->style = $style;
        $this->pseudoElement = $pseudoElement;
    }

    public function acceptWBlock($block) : ?string
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : ?string
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

    protected function apply(WPageObject $pageObject) : ?string
    {
        $computedStyle = $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_GET_COMPUTED_STYLE, [$this->style, $this->pseudoElement]));

        return $computedStyle;
    }

    protected const SCRIPT_GET_COMPUTED_STYLE = <<<EOF
function getStyle(element, style, pseudoElement = null) {
    return window.getComputedStyle(element, pseudoElement)[style];
}

return getStyle(arguments[0], arguments[1], arguments[2]);
EOF;
}