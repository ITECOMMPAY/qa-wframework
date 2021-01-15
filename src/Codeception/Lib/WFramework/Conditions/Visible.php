<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\TraverseFromRootExplanation;
use Codeception\Lib\WFramework\Operations\Execute\ExecuteScriptOnThis;
use Codeception\Lib\WFramework\Operations\Mouse\MouseScrollTo;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class Visible extends AbstractCondition
{
    static $isSafari;

    public function getName() : string
    {
        return "отображается на странице?";
    }

    public function acceptWBlock($block) : bool
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : bool
    {
        return $this->apply($element);
    }

    public function acceptWCollection($collection) : bool
    {
        if ($collection->isEmpty())
        {
            return false;
        }

        return $this->apply($collection->getFirstElement());
    }

    protected function apply(WPageObject $pageObject) : bool
    {
        try
        {
            if (!$pageObject->returnSeleniumElement()->isExist())
            {
                return false;
            }

            if ($this->isSafari())
            {
                // Safari пока не поддерживает /session/{}/element/{}/displayed
                return (bool) $pageObject->accept(new ExecuteScriptOnThis(static::SCRIPT_VISIBLE));
            }

            $pageObject->accept(new MouseScrollTo());

            return $pageObject->returnSeleniumElement()->isDisplayed() ?? false;
        }
        catch (NoSuchElementException $e)
        {
            return false;
        }
        catch (StaleElementReferenceException $e)
        {
            return false;
        }
    }

    protected function isSafari() : bool
    {
        if (!isset(static::$isSafari))
        {
            static::$isSafari = strtolower(TestProperties::getValue('browser', '')) === 'safari';
        }

        return static::$isSafari;
    }

    protected function getExplanationClasses() : array
    {
        return [TraverseFromRootExplanation::class];
    }

    //https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js
    protected const SCRIPT_VISIBLE  = <<<EOF
return !!( arguments[0].offsetWidth || arguments[0].offsetHeight || Array.from(arguments[0].getClientRects()).filter(function(el){return el.width > 0 && el.height > 0;}).length );
EOF;
}
