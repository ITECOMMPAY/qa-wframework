<?php


namespace Codeception\Lib\WFramework\WOperations\Execute;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\ProxyWebElement\ProxyWebDriverActions;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WOperations\AbstractOperation;

class ExecuteActions extends AbstractOperation
{
    /**
     * Инициализирует выполнение Selenium Actions для данного элемента.
     *
     * Selenium Actions позволяет эмулировать комплексные действия пользователя:
     * https://seleniumhq.github.io/selenium/docs/api/java/org/openqa/selenium/interactions/Actions.html
     *
     * После составления цепочки действий её необходимо отправить на выполнение, с помощью метода perform().
     *
     * Пример:
     *
     *     $facadeWebElement
     *                      ->execute()
     *                      ->actions()
     *                      ->moveToElement(0, 0)
     *                      ->contextClick()
     *                      ->perform()
     *                      ;
     */
    public function __construct() { }

    public function acceptWBlock($block) : ProxyWebDriverActions
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : ProxyWebDriverActions
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : ProxyWebDriverActions
    {
        WLogger::logDebug('Выполняем Selenium Actions для элемента');

        return $pageObject
                    ->getProxyWebElement()
                    ->executeActions()
                    ;
    }
}
