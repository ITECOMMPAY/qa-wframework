<?php


namespace dodge\Tests;


use dodge\DodgeTester;
use dodge\Helper\Steps\DodgeSteps;

/**
 * Class exampleCest
 *
 * Примеры тестов.
 * Тесты работают только под Линукс.
 *
 * Прогон локально:    ./vendor/bin/codecept run webui exampleCest -c ./tests/dodge --env dodge-loc,dodge-loc-chrome,dodge-loc-1920
 * Через BrowserStack: ./vendor/bin/codecept run webui exampleCest -c ./tests/dodge --env dodge-bs,dodge-bstack-chrome,dodge-bstack-1920,dodge-windows
 *
 * @package dodge\cases
 */
class exampleCest
{
    protected $beforeSuite = false;

    public function _before(DodgeTester $I)
    {
        // В Codeception нет beforeSuite - эмулируем его таким образом. Код внутри if будет выполнен только один раз
        // перед прогоном всех тестов в Cest
        if (!$this->beforeSuite)
        {


            $this->beforeSuite = true;
        }
    }

    /**
     * При использовании PhpStorm не забываем:
     *     Ctrl+Q (не на Маке) - показывает хелпу по методу, если она есть
     *     Ctrl+Shift+I - открывает исходники метода во всплывающем окне
     *     Ctrl+LKM или F4 - переходит к исходникам метода. А боковая кнопка мышки возвращает обратно.
     *     Alt+7 - переходит на вкладку структуры класса.
     *             При просмотре PageObject'а (блока или элемента) на ней следует отключить Show Protected Members и
     *             Show Private Members и включить Show Inherited - будут отображены все методы, которые можно использовать из тестов.
     *             Первым делом следует обратить внимание на неунаследованные методы.
     *
     * Среди унаследованных методов PageObject'ов можно выделить четыре больших группы:
     *     - is-методы проверяют некоторое условие для PO и возвращают true или false;
     *     - should-методы - это умные ожидания. Они ждут выполнения условия в течении некоторого таймаута
     *                       и если оно не выполнилось - валят тест;
     *     - return-методы проваливаются в глубины фреймворка. Среди них следует выделить:
     *                       returnCodeceptionActor(), который возвращает $I;
     *                       returnSeleniumElement(), который возвращает фасад над всякими примитивными методами Селениума.
     *     - get-методы. Среди них обычно нужны геттеры детей PageObject'а и методы для получения текста.
     */

    //--------------------

    public function exampleTest(DodgeTester $I, DodgeSteps $steps)
    {
        $I->wantToTest('Проверить чё-то там');

        $steps::$frontPageSteps
                        ->openSite()
                        ->closePopup()
                        ->setZip()
                        ->openVehiclesMenu()
                        ->checkPrices()
                        ->selectVehicle('Alias: Challenger')
                        ->checkZip()
                        ->startBuildingModel()
                        ->selectBuyOption()
                        ->selectModel('Alias: SRT HELLCAT REDEYE WIDEBODY')
                        ->checkModelName()
                        ->setModelColor('Alias: White Knuckle')
                        ->selectDecal('Alias: Dual Silver Stripes')
                        ;
    }
}
