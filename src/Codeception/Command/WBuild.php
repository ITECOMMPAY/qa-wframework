<?php


namespace Codeception\Command;


use Codeception\Configuration;
use Codeception\CustomCommandInterface;
use Codeception\Lib\WFramework\Generator\WProjectStructure;
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
        $supportDir = Configuration::supportDir();

        $suites = $this->getSuites();

        foreach ($suites as $suite)
        {
            $suiteSettings = $this->getSuiteConfig($suite);

            if (!$this->suiteUsesWFramework($suiteSettings))
            {
                continue;
            }

            $actorNameShort = $suiteSettings['actor'];
            $namespace = $suiteSettings['namespace'] ?? '';

            if (!empty($namespace))
            {
                $projectName = ucfirst($namespace);
            }
            else
            {
                $projectName = ucfirst($actorNameShort);

                $length = strpos($projectName, 'Tester');

                if ($length !== false)
                {
                    $projectName = substr($projectName, 0, $length + 1);
                }
            }

            (new WProjectStructure($projectName, $namespace, $actorNameShort, $supportDir))->build();
        }

        return 0;
    }

    protected function suiteUsesWFramework(array $suiteSettings) : bool
    {
        $enabledModules = $suiteSettings['modules']['enabled'];

        foreach ($enabledModules as $module)
        {
            $moduleName = $module;

            if (is_array($module))
            {
                $moduleName = array_keys($module);
                $moduleName = reset($moduleName);
            }

            if (strpos($moduleName, 'WebTestingModule') !== false)
            {
                return true;
            }
        }

        return false;
    }
}
