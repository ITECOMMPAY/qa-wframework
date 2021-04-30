<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class GetBoundingClientRectVisible extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем видимый boundingClientRect";
    }

    /**
     * Возвращает видимый boundingClientRect элемента
     */
    public function __construct() {}

    public function acceptWBlock($block) : Rect
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : Rect
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : Rect
    {
        return Rect::fromDOMRect($pageObject->returnSeleniumElement()->executeScriptOnThis(static::GET_VISIBLE_BOUNDING_CLIENT_RECT));
    }

    protected const GET_VISIBLE_BOUNDING_CLIENT_RECT = <<<EOF
    return getVisibleClientRect(arguments[0]);

    function getVisibleClientRect(element) {
        let rect = element.getBoundingClientRect();
    
        let docClientWidth = document.documentElement.clientWidth;
        let docClientHeight = document.documentElement.clientHeight;
    
        let x = ((rect.left < 0) ? 0 : ((rect.left > docClientWidth) ? docClientWidth : rect.left));
        let y = ((rect.top < 0) ? 0 : ((rect.top > docClientHeight) ? docClientHeight : rect.top));
    
        var width = 0;
    
        if (rect.x < 0) {
            width = rect.width + rect.left;
        } else if (rect.left >= 0 && rect.right < docClientWidth) {
            width = rect.width;
        } else {
            width = docClientWidth - rect.left;
        }
    
        if (width < 0) {
            width = 0;
        }
    
        var height = 0;
    
        if (rect.top < 0) {
            height = rect.height + rect.top;
        } else if (rect.top >= 0 && rect.bottom < docClientHeight) {
            height = rect.height;
        } else {
            height = docClientHeight - rect.top;
        }
    
        if (height < 0) {
            height = 0;
        }
    
        return DOMRectReadOnly.fromRect({x, y, width, height});
    }
EOF;
}