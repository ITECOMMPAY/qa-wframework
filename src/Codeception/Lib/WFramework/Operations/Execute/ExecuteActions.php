<?php


namespace Codeception\Lib\WFramework\Operations\Execute;


use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElementActions;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\Operations\AbstractOperation;

class ExecuteActions extends AbstractOperation
{
    public function getName() : string
    {
        return "выполняем Selenium Actions";
    }

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

    public function acceptWBlock($block) : ProxyWebElementActions
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : ProxyWebElementActions
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : ProxyWebElementActions
    {
        return $pageObject
                    ->shouldExist()
                    ->returnSeleniumElement()
                    ->executeActions()
                    ;
    }
}
