<?php


namespace Codeception\Command;


use Codeception\Configuration;
use Codeception\CustomCommandInterface;
use Codeception\Lib\WFramework\Generator\WProjectStructure;
use Codeception\Lib\WFramework\Helpers\Codeception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WBuild extends Command implements CustomCommandInterface
{
    use \Codeception\Command\Shared\Config;

    public static function getCommandName()
    {
        return 'wbuild';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = Configuration::projectDir();
        $supportDir = Configuration::supportDir();
        $testsDir   = Configuration::testsDir();
        $suites     = Configuration::suites();

        foreach ($suites as $suite)
        {
            $suiteSettings   = $this->getSuiteConfig($suite);
            $actorClassShort = $suiteSettings['actor'];
            $namespace       = $suiteSettings['namespace'] ?? '';

            $frameworkConfig = Codeception::getModuleConfig('WebTestingModule', $suiteSettings);

            if ($frameworkConfig === null)
            {
                continue;
            }

            $commonDirs = $frameworkConfig['commonDirs'] ?? [];

            if (!empty($namespace))
            {
                $projectName = ucfirst($namespace);
            }
            else
            {
                $projectName = ucfirst($actorClassShort);

                $length = strpos($projectName, 'Tester');

                if ($length !== false)
                {
                    $projectName = substr($projectName, 0, $length + 1);
                }
            }

            (new WProjectStructure($projectName, $actorClassShort, $namespace, $projectDir, $supportDir, $testsDir, $commonDirs, false))->build();
        }

        return 0;
    }
}
