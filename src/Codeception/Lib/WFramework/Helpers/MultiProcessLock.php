<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\PortAlreadyInUseException;
use Codeception\Lib\WFramework\Logger\WLogger;

class MultiProcessLock
{
    /**
     * @var int
     */
    protected $portNumber;

    /**
     * @var false|resource
     */
    protected $stream;

    /**
     * @var int
     */
    protected $timeoutSec;

    public function __construct(int $portNumber, int $timeoutSec = 300)
    {
        $this->portNumber = $portNumber;
        $this->timeoutSec = $timeoutSec;
    }

    /**
     * @param callable|null $checkActionCompleted - опциональный метод для проверки того, что действие, ради которого лочили процессы,
     *                                              успешно завершено. Например, что один из экземпляров скрипта успешно запустил необходимую программу.
     *                                              Этот метод необходим потому что, когда PHP-скрипт лочит порт и запускает какую-нибудь программу,
     *                                              после завершения работы PHP-скрипта лок не будет снят, пока запущенная программа не завершится.
     *                                              Должен возвращать булево значение.
     *                                              Должен быть public, иначе PHP вернёт мутную ошибку: Argument 1 passed to *** must be callable or null, array given
     * @throws PortAlreadyInUseException
     */
    public function lock(callable $checkActionCompleted = null)
    {
        $this->stream = false;
        $timeout = time() + $this->timeoutSec;

        while (!$this->stream && time() < $timeout)
        {
            $this->stream = @stream_socket_server("tcp://127.0.0.1:{$this->portNumber}", $errno, $errmg);

            if ($this->stream !== false)
            {
                return;
            }

            if ($checkActionCompleted !== null && $checkActionCompleted())
            {
                return;
            }

            WLogger::logDebug($this, 'Другой экземпляр скрипта пытается настроить и запустить Selenium Server - ждём');
            sleep(3);
        }

        if (!$this->stream)
        {
            throw new PortAlreadyInUseException("Другой процесс висит на порту {$this->portNumber}. Нужно его убить.");
        }
    }

    public function unlock()
    {
        if ($this->stream === false)
        {
            return;
        }

        fclose($this->stream);
    }
}