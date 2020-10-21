<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 16.08.18
 * Time: 14:22
 */

namespace Common\Module\WFramework\Helpers;

/**
 * Class Process
 * Класс оборачивает Юниксовый процесс
 * @package ECP_QA
 */
class UnixProcess
{
    private $pid;
    private $command;

    /**
     * Process constructor.
     * @param string $commandLine - строка для вызова команды, которую необходимо выполнить в отдельном процессе.
     */
    public function __construct(string $commandLine)
    {
        $this->command = escapeshellcmd($commandLine);
    }

    private function runCommand()
    {
        $command = 'nohup ' . $this->command.' > /dev/null 2>&1 & echo $!';
        $output = array();
        exec($command ,$output);
        $this->pid = (int) $output[0];
    }

    /**
     * @return bool - True, если запущенный процесс всё-ещё выполняется.
     */
    public function isRunning() : bool
    {
        $command = 'ps -p ' . $this->pid;

        $output = array();
        exec($command,$output);

        if (!isset($output[1]))
        {
            return False;
        }

        return True;
    }

    /**
     * Запускает процесс.
     * @return int - PID процесса
     */
    public function start() : int
    {
        $this->runCommand();

        return $this->pid;
    }

    /**
     * Останавливает процесс.
     */
    public function stop()
    {
        $command = 'kill ' . $this->pid;
        exec($command);
    }

    /**
     * @return int - количество ядер процессора
     */
    public static function getCoresNumber() : int
    {
        $command = 'cat /proc/cpuinfo | grep processor | wc -l';

        $output = array();
        exec($command,$output);

        return $output[0];
    }
}
