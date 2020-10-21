<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 13.03.19
 * Time: 13:50
 */

namespace Common\Module\WFramework\WebObjects\Base\WElement\Import;

use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WLocator\WLocator;

class WFromLocator extends WFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $name, WLocator $locator, bool $relative = true)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->name = $name;
        $this->locator = $locator;
        $this->relative = $relative;
        $this->facadeWebElement = null;

        if ($relative === true && $locator->getMechanism() === 'xpath' && $locator->getValue()[0] !== '.')
        {
            WLogger::logWarning('!!!' . $name . ' [' . $locator->getValue() . '] : XPath-локаторы для относительных элементов, должны начинаться с точки');

            $relativeLocator = WLocator::xpath('.' . $locator->getValue());
            $this->locator = $relativeLocator;
        }
    }
}
