<?php


namespace Codeception\Lib\WFramework\WOperations\Get;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;
use Facebook\WebDriver\WebDriverDimension;

class GetWindowInnerSize extends AbstractOperation
{
    /**
     * Возвращает размер вьюпорта окна браузера (innerWidth x innerHeight)
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
        WLogger::logDebug('Получаем размер viewport\'а окна браузера');

        $size = $pageObject->getProxyWebElement()->executeScript('return {"width": window.innerWidth, "height": window.innerHeight};');

        WLogger::logDebug('Viewport имеет размер: ' . $size['width'] . 'x' . $size['height']);

        return new WebDriverDimension($size['width'], $size['height']);
    }
}
