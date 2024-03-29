<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 11.04.19
 * Time: 16:14
 */

namespace Codeception\Lib\WFramework\WebObjects\Base\WCollection\Import;

use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WsFromFirstElement extends WsFrom
{
    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(WElement $webElement)
    {
        //Здесь нельзя вызывать родительский конструктор

        $this->proxyWebElements = null;
        $this->firstElement = $webElement;
    }
}
