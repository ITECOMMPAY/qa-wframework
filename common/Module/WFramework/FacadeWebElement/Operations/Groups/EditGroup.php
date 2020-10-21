<?php


namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;

/**
 * Категория методов FacadeWebElement, которая содержит набор методов для правки кода страницы.
 *
 * @package Common\Module\WFramework\FacadeWebElement\Operations\Groups
 */
class EditGroup extends OperationsGroup
{
    public function innerHTML(string $text) : EditGroup
    {
        WLogger::logDebug('Задаём innerHTML равное: ' . $text);

        $this->getProxyWebElement()->executeScriptOnThis('arguments[0].innerHTML = arguments[1];', [$text]);

        return $this;
    }
}
