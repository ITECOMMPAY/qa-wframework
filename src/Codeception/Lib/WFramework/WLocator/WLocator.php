<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 10.04.19
 * Time: 15:28
 */

namespace Codeception\Lib\WFramework\WLocator;


use Facebook\WebDriver\WebDriverBy;

/**
 * Класс для хранения локаторов Селениума.
 *
 * Следует использовать его, а не WebDriverBy, чтобы облегчить последующие доработки фреймворка,
 * например для использования локализированных локаторов.
 *
 * @package Common\Module\WFramework\WLocator
 */
class WLocator extends WebDriverBy
{
    public static function fromWebDriverBy(WebDriverBy $by)
    {
        return new WLocator($by->getMechanism(), $by->getValue());
    }

    public function isEmpty() : bool
    {
        return false;
    }

    public function isHtmlRoot() : bool
    {
        return $this->getValue() === '/html';
    }

    public function __toString() : string
    {
        $mechanism = $this->getMechanism();
        $value = $this->getValue();
        return "[$mechanism: $value]";
    }
}
