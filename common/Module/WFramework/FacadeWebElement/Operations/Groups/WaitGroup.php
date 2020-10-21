<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 13:27
 */

namespace Common\Module\WFramework\FacadeWebElement\Operations\Groups;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\Exceptions\FacadeWebElementOperations\WaitUntilElement;
use Common\Module\WFramework\Exceptions\FacadeWebElementOperations\WaitWhileElement;
use Common\Module\WFramework\FacadeWebElement\Operations\OperationsGroup;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use function usleep;


class WaitGroup extends OperationsGroup
{
    /**
     * Ожидает, пока для данного элемента начнут выполняться условия,
     * или не пройдёт, заданный в настройках модуля, elementTimeout.
     *
     * @param Cond $condition - условия
     * @return WaitGroup
     * @throws WaitUntilElement - не удалось дождаться выполнения условий для данного элемента
     */
    public function until(Cond $condition) : WaitGroup
    {
        WLogger::logDebug('Ждём пока заданные условия выполнятся для элемента');

        $timeout = (int) TestProperties::getValue('elementTimeout');

        $deadLine = microtime(True) + $timeout;

        while (microtime(True) < $deadLine)
        {
            if ($condition->check($this->facadeWebElement))
            {
                return $this;
            }

            usleep(500000);
        }

        throw new WaitUntilElement('Не удалось дождаться появления состояния: ' . $condition->toString());
    }

    /**
     * Ожидает, пока для данного элемента перестанут выполняться условия,
     * или не пройдёт, заданный в настройках модуля, elementTimeout.
     *
     * @param Cond $condition - условия
     * @return WaitGroup
     * @throws WaitWhileElement - не удалось дождаться окончания выполнения условий для данного элемента
     */
    public function while(Cond $condition) : WaitGroup
    {
        WLogger::logDebug('Ждём пока заданные условия перестанут выполняться для элемента');

        $timeout = (int) TestProperties::getValue('elementTimeout');

        $deadLine = microtime(True) + $timeout;

        while (microtime(True) < $deadLine)
        {
            if (!$condition->check($this->facadeWebElement))
            {
                return $this;
            }

            usleep(500000);
        }

        throw new WaitWhileElement('Не удалось дождаться смены состояния: ' . $condition->toString());
    }

    /**
     * Ожидает, заданный в настройках модуля, elementTimeout.
     *
     * @return WaitGroup
     */
    public function forTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём заданный таймаут');

        $timeout = (int) TestProperties::getValue('elementTimeout');

        sleep($timeout);

        return $this;
    }

    /**
     * Ожидает половину от, заданного в настройках модуля, elementTimeout.
     *
     * @return WaitGroup
     */
    public function forHalfTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём половину от заданного таймаута');

        $timeout = (int) TestProperties::getValue('elementTimeout') * 1000000;

        $timeout = intdiv($timeout, 2);

        usleep($timeout);

        return $this;
    }

    /**
     * Ожидает четверть от, заданного в настройках модуля, elementTimeout.
     *
     * @return WaitGroup
     */
    public function forQuarterTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём четверть от заданного таймаута');

        $timeout = (int) TestProperties::getValue('elementTimeout') * 1000000;

        $timeout = intdiv($timeout, 4);

        usleep($timeout);

        return $this;
    }

    /**
     * Ожидает восьмую часть от, заданного в настройках модуля, elementTimeout.
     *
     * @return WaitGroup
     */
    public function forEighthTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём восьмую часть от заданного таймаута');

        $timeout = (int) TestProperties::getValue('elementTimeout') * 1000000;

        $timeout = intdiv($timeout, 8);

        usleep($timeout);

        return $this;
    }

    /**
     * Ожидает шестнадцатую часть от, заданного в настройках модуля, elementTimeout.
     *
     * @return WaitGroup
     */
    public function forSixteenthTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём одну шестнадцатую часть от заданного таймаута');

        $timeout = (int) TestProperties::getValue('elementTimeout') * 1000000;

        $timeout = intdiv($timeout, 16);

        usleep($timeout);

        return $this;
    }
}
