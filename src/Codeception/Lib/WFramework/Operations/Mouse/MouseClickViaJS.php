<?php


namespace Codeception\Lib\WFramework\Operations\Mouse;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class MouseClickViaJS extends AbstractOperation
{
    public function getName() : string
    {
        return "кликаем с помощью JavaScript";
    }

    /**
     * Осуществляет клик на данном элементе с помощью JavaScript
     */
    public function __construct() { }

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
        $collection->getElementsArray()->map([$this, 'apply']);
    }

    protected function apply(WPageObject $pageObject)
    {
        $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_CLICK);
    }

    protected const SCRIPT_CLICK = <<<EOF
let element = arguments[0];

if (!typeof element.click === 'function') {
    element.dispatchEvent(new MouseEvent('click', {view: window, bubbles:true, cancelable: true}))
    return;
}

element.click();
EOF;

}
