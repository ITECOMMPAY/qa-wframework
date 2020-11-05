<?php

// Сюда можно дописать любые аргументы, кроме -d / --daemon
$bs_arguments = "--local-identifier :local_id --key :bs_key --force --only-automate --parallel-runs 50";

// Cюда нужно вписать ключ BrowserStack
$bs_key = '***';

/**
 * В Дженкинсе запускать этот скрипт перед прогоном тестов.
 *
 * В случае Jenkins Pipeline с помощью команды:
 *      sh "JENKINS_NODE_COOKIE=dontKillMe php ./common/Helpers/BSStarter.php"
 *
 * В остальных случаях:
 *         "BUILD_ID=dontKillMe php ./common/Helpers/BSStarter.php"
 *
 * dontKillMe нужен чтобы Дженкинс не убивал запущенный BrowserStack после прогона
 * (https://wiki.jenkins.io/display/JENKINS/ProcessTreeKiller).
 */





if(file_exists('../../../../vendor/autoload.php'))
{
    require_once '../../../../vendor/autoload.php';
}
else
{
    require_once 'vendor/autoload.php';
}

function killAllInstances()
{
    echo 'Убиваем всех агентов BS' . PHP_EOL;

    $command = "ps aux | grep [B]rowserStackLocal | awk '{print $2}'";

    $output = array();
    exec($command,$output);

    if (empty($output))
    {
        return;
    }

    foreach ($output as $pid)
    {
        if ($pid < 300)
        {
            continue;
        }

        exec("kill $pid");
    }
}

// В памяти есть запущенный агент BrowserStack?
function isRunning() : bool
{
    $command = "ps aux | grep [B]rowserStackLocal";

    $output = array();
    exec($command,$output);

    return !empty($output);
}

// В памяти есть запущенный агент BrowserStack с нашим local-identifier?
function isRunningFromThisScript() : bool
{
    $localId = getLocalId();
    $command = "ps aux | grep [B]rowserStackLocal | grep 'local-identifier $localId'";

    $output = array();
    exec($command,$output);

    return !empty($output);
}

// В памяти есть запущенный агент BrowserStack с нашим local-identifier и необходимыми параметрами?
function isRunningWithSameArguments() : bool
{
    $localId = getLocalId();
    $command = "ps aux | grep [B]rowserStackLocal | grep 'local-identifier $localId'";

    $output = array();
    exec($command,$output);

    if (empty($output))
    {
        return false;
    }

    global $bs_arguments;
    global $key;

    $searchString1 = strtr($bs_arguments, [':local_id' => $localId, ':bs_key' => '***']) . ' -daemonInstance'; // BrowserStack иногда маскирует ключ, а иногда и нет
    $searchString2 = strtr($bs_arguments, [':local_id' => $localId, ':bs_key' => $key]) . ' -daemonInstance';

    foreach ($output as $line)
    {
        if (strpos($line, $searchString1) === false && strpos($line, $searchString2) === false)
        {
            continue;
        }

        return true;
    }

    return false;
}

function waitStart() : bool
{
    $timeout = time() + 7;

    while (time() < $timeout && !isRunningWithSameArguments()) // Т.к. мы ждём запуска не абы какого, а нужного нам агента
    {
        sleep(1);
    }

    return isRunningWithSameArguments();
}

function waitStop() : bool
{
    $timeout = time() + 7;

    while (time() < $timeout && isRunningFromThisScript()) // Т.к. мы ждём остановки всех агентов, которые запускались из этого скрипта
    {
        sleep(1);
    }

    return !isRunningFromThisScript();
}

function waitKill() : bool
{
    $timeout = time() + 7;

    while (time() < $timeout && isRunning())
    {
        sleep(1);
    }

    return !isRunning();
}

function startInstance()
{
    global $bs_arguments;
    global $bs_key;
    $binaryPath = (new \BrowserStack\LocalBinary())->binary_path();
    $localId = getLocalId();
    $command = "$binaryPath $bs_arguments --daemon start";

    run(strtr($command, [':local_id' => $localId, ':bs_key' => $bs_key]));
}

function stopInstance()
{
    echo 'Останавливаем агента BS' . PHP_EOL;

    $binaryPath = (new \BrowserStack\LocalBinary())->binary_path();
    $localId = getLocalId();
    $command = "$binaryPath --local-identifier :local_id --daemon stop";

    run(strtr($command, [':local_id'=> $localId]));
}

function run(string $command)
{
    $command = 'nohup ' . $command.' > /dev/null 2>&1 & echo $!';
    $output = array();
    exec($command, $output);
    $pid = (int) $output[0];

    if ($pid < 0)
    {
        throw new \Exception('Не получилось запустить команду: ' . $command);
    }
}

function getLocalId() : string
{
    return preg_replace("/[^A-Za-z0-9]/", '', php_uname());
}

$stream = false;

function lock()
{
    echo 'Лочим порт на себя' . PHP_EOL;

    global $stream;

    $stream = waitLock(300);

    if (isRunningWithSameArguments())
    {
        return;
    }

    // Возможно другой экземпляр скрипта успешно запустил BS, но был убит до того как снял лок
    if (!$stream && isRunningFromThisScript())
    {
        echo 'Не получилось залочить порт - возможно он висит на другом инстансе агента' . PHP_EOL;

        stopInstance();

        if (!waitStop()) // Запущенный BS не только держит лок, но ещё и не хочет закрываться по-хорошему
        {
            echo 'Не получилось остановить другого агента - убиваем его' . PHP_EOL;

            killAllInstances();
        }

        $stream = waitLock(60);

        if (isRunningWithSameArguments())
        {
            return;
        }
    }

    if (!$stream)
    {
        throw new \Exception('Другой процесс висит на порту 25441. Нужно его убить.');
    }
}

function waitLock(int $timeout)
{
    $stream = false;
    $maxTime = time() + $timeout;

    while (!$stream && time() < $maxTime)
    {
        $stream = @stream_socket_server('tcp://127.0.0.1:25441', $errno, $errmg);

        if ($stream !== false)
        {
            echo 'Успешно залочили порт' . PHP_EOL;

            break;
        }

        if (isRunningWithSameArguments())
        {
            echo 'Не получилось залочить порт, но кто-то уже запустил нужного нам агента' . PHP_EOL;

            break;
        }

        sleep(3);
    }

    return $stream;
}

function unlock()
{
    echo 'Разлочиваем порт' . PHP_EOL;

    global $stream;

    if ($stream === false)
    {
        return;
    }

    fclose($stream);
}

function runBrowserStack()
{
    if (isRunningWithSameArguments())                             // Другой скрипт уже запустил BS с теми же параметрами
    {
        echo 'Агент уже запущен другим процессом' . PHP_EOL;

        return;
    }

    lock();                                 // Лочим на себя - остальные скрипты должны подождать, пока мы разлочим порт

    if (isRunningWithSameArguments())        // Пока мы ждали лока - другой скрипт уже запустил BS с теми же параметрами
    {
        echo 'Агент уже запущен другим процессом' . PHP_EOL;

        unlock();
        return;
    }

    if (isRunningFromThisScript())      // В памяти есть BS, который был запущен с помощью этого скрипта - закрываем его
    {
        echo 'В памяти есть агент запущенный с другими параметрами - завершаем его работу' . PHP_EOL;

        stopInstance();
        waitStop();
    }

    if (isRunning())                                       // В памяти остались BS запущенные кем-то другим - убиваем их
    {
        echo 'В памяти остались агенты - убиваем их' . PHP_EOL;

        killAllInstances();
        waitKill();
    }

    echo 'Запускаем агента' . PHP_EOL;

    startInstance();                                                                  // Запускаем BS и ждём его запуска
    waitStart();
    unlock();

    if (isRunningWithSameArguments())                                                              // BS успешно запущен
    {
        echo 'Агент успешно запущен' . PHP_EOL;

        return;
    }

    throw new \Exception('Не получилось запустить агента BrowserStack');
}

echo PHP_EOL . 'Запускаем локального агента BrowserStack: ' . PHP_EOL;
runBrowserStack();
