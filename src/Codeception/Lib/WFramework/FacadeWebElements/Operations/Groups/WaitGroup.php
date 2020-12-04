<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 13:27
 */

namespace Codeception\Lib\WFramework\FacadeWebElements\Operations\Groups;

use Codeception\Lib\WFramework\CollectionCondition\CCond;
use Codeception\Lib\WFramework\Exceptions\FacadeWebElementOperations\WaitUntilElement;
use Codeception\Lib\WFramework\Exceptions\FacadeWebElementOperations\WaitWhileElement;
use Codeception\Lib\WFramework\FacadeWebElements\Operations\OperationsGroup;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;

class WaitGroup extends OperationsGroup
{
    public function until(CCond $condition) : WaitGroup
    {
        WLogger::logDebug('Ждём пока заданные условия не выполнятся для коллекции элементов');

        $timeout = (int) TestProperties::getValue('collectionTimeout');

        $deadLine = microtime(True) + $timeout;

        while (microtime(True) < $deadLine)
        {
            $this->facadeWebElements->refresh();

            if ($condition->check($this->facadeWebElements))
            {
                return $this;
            }

            usleep(1000000);
        }

        throw new WaitUntilElement('Не удалось дождаться появления состояния: ' . $condition->toString());
    }

    public function while(CCond $condition) : WaitGroup
    {
        WLogger::logDebug('Ждём пока заданные условия не перестанут выполняться для коллекции элементов');

        $timeout = (int) TestProperties::getValue('collectionTimeout');

        $deadLine = microtime(True) + $timeout;

        while (microtime(True) < $deadLine)
        {
            $this->facadeWebElements->refresh();

            if (!$condition->check($this->facadeWebElements))
            {
                return $this;
            }

            usleep(1000000);
        }

        throw new WaitWhileElement('Не удалось дождаться смены состояния: ' . $condition->toString());
    }

    public function forTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём заданный таймаут (коллекция)');

        $timeout = (int) TestProperties::getValue('collectionTimeout');

        sleep($timeout);

        return $this;
    }

    public function forHalfTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём половину от заданного таймаута (коллекция)');

        $timeout = (int) TestProperties::getValue('collectionTimeout') * 1000000;

        $timeout = intdiv($timeout, 2);

        usleep($timeout);

        return $this;
    }

    public function forQuarterTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём четверть от заданного таймаута (коллекция)');

        $timeout = (int) TestProperties::getValue('collectionTimeout') * 1000000;

        $timeout = intdiv($timeout, 4);

        usleep($timeout);

        return $this;
    }

    public function forEighthTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём восьмую часть от заданного таймаута (коллекция)');

        $timeout = (int) TestProperties::getValue('collectionTimeout') * 1000000;

        $timeout = intdiv($timeout, 8);

        usleep($timeout);

        return $this;
    }

    public function forSixteenthTimeout() : WaitGroup
    {
        WLogger::logDebug('Ждём одну шестнадцатую часть от заданного таймаута');

        $timeout = (int) TestProperties::getValue('collectionTimeout') * 1000000;

        $timeout = intdiv($timeout, 16);

        usleep($timeout);

        return $this;
    }
}
