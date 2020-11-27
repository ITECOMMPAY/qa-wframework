<?php


namespace Common\Module\WFramework\Command;


use Codeception\Configuration;
use Codeception\CustomCommandInterface;
use Common\Module\WFramework\Generator\WProjectStructure;
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
        $actorSuffix = $this->getGlobalConfig()['actor_suffix'] ?? 'Tester';

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

            (new WProjectStructure($actorNameShort, $actorSuffix, $supportDir, $namespace))->build();
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

            if (strpos($moduleName, 'WFramework') !== false)
            {
                return true;
            }
        }

        return false;
    }
}
