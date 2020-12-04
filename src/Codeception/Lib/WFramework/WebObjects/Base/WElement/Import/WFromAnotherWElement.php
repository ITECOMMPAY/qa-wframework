<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 19.04.19
 * Time: 15:42
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WElement\Import;


use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WFromAnotherWElement extends WFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(WElement $element)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->name = $element->getName();
        $this->locator = $element->getLocator();
        $this->relative = $element->isRelative();
        $this->facadeWebElement = $element->returnSeleniumElement();
        $this->parentElement = $element->getParent();
    }
}
