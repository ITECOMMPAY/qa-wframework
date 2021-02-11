<?php


namespace Codeception\Lib\WFramework\Selenium;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Helpers\CurrentOS;
use Symfony\Component\Process\Process;

abstract class AbstractDriverManager
{
    abstract public function __construct(array $config);

    abstract public function get() : string;

    protected function getOS() : string
    {
        $os = CurrentOS::get();

        if ($os !== CurrentOS::LINUX && $os !== CurrentOS::MAC)
        {
            throw new UsageException('Данный модуль работает только под GNU/Linux и Mac. Текущая ОС: ' . $os);
        }

        return $os;
    }

    protected function getBrowserInfo(array $binaryNames, string $versionFlag, string $regex) : array
    {
        $result = [
            'name' => '',
            'version' => ''
        ];

        foreach ($binaryNames as $binaryName)
        {
            $proc = new Process([$binaryName, $versionFlag]);
            $proc->run();
            $output = $proc->getOutput();

            if (empty($output))
            {
                continue;
            }

            preg_match($regex, $output, $matches);

            if (isset($matches[1]))
            {
                $result['name'] = $binaryName;
                $result['version'] = $matches[1];

                return $result;
            }
        }

        return $result;
    }
}