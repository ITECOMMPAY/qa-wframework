<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Facebook\WebDriver\WebDriverDimension;

class GetLayoutViewportSize extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем размер вьюпорта окна браузера (innerWidth x innerHeight)";
    }

    /**
     * Возвращает размер вьюпорта окна браузера (innerWidth x innerHeight)
     *
     * https://developer.mozilla.org/en-US/docs/Glossary/layout_viewport
     *
     */
    public function __construct() {}

    public function acceptWBlock($block) : WebDriverDimension
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : WebDriverDimension
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : WebDriverDimension
    {
        $size = $pageObject->returnSeleniumElement()->executeScript('return {"width": window.innerWidth, "height": window.innerHeight};');

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
