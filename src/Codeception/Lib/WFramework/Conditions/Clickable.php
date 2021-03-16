<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\ElementClickInterceptedExplanation;
use Codeception\Lib\WFramework\Explanations\VisibleExplanation;
use Codeception\Lib\WFramework\Helpers\Rect;
use Codeception\Lib\WFramework\Operations\Get\GetBoundingClientRectVisible;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;

class Clickable extends AbstractCondition
{
    protected $invisible = false;

    public function getName() : string
    {
        return "кликабелен?";
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function apply(WPageObject $pageObject) : bool
    {
        $visible = $pageObject->accept(new Visible());

        if (!$visible)
        {
            $this->invisible = true;
            return false;
        }

        /** @var Rect $visibleRect */
        $visibleRect = $pageObject->accept(new GetBoundingClientRectVisible());

        if ($visibleRect->width === 0 || $visibleRect->height === 0)
        {
            return false;
        }

        return $pageObject->returnSeleniumElement()->executeScriptOnThis(static::SCRIPT_CLICKABLE_AT_POINT, [floor($visibleRect->width / 2), floor($visibleRect->height / 2)]);
    }

    protected function getExplanationClasses() : array
    {
        if ($this->invisible)
        {
            return [VisibleExplanation::class];
        }

        return [ElementClickInterceptedExplanation::class];
    }

    protected const SCRIPT_CLICKABLE_AT_POINT = <<<EOF
    function clickableAtPoint(element, viewportX, viewportY) {
        var actualElement = document.elementFromPoint(viewportX, viewportY);
        
        while (actualElement !== null)
        {
            if (actualElement === element)
            {
                return true;
            }
            
            actualElement = actualElement.parentElement;
        }
        
        return false;
    }
    
    return clickableAtPoint(arguments[0], arguments[1], arguments[2]);
EOF;
}