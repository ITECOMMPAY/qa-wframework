<?php


namespace Codeception\Extension;


use Codeception\Configuration;
use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Generator\WProjectStructure;
use Codeception\Lib\WFramework\Helpers\Codeception;

class WAutoRebuild extends \Codeception\Extension
{
    public static $events = [
        Events::SUITE_INIT => 'updateProjectStructure'
    ];

    public function updateProjectStructure(SuiteEvent $e)
    {
        if ($this->options['no-rebuild'] ?? false)
        {
            return;
        }

        $projectDir      = Configuration::projectDir();
        $supportDir      = Configuration::supportDir();
        $testsDir        = Configuration::testsDir();
        $suiteSettings   = $e->getSettings();
        $actorClassShort = $suiteSettings['actor'];
        $namespace       = $suiteSettings['namespace'] ?? '';

        $frameworkConfig = Codeception::getModuleConfig('WebTestingModule', $suiteSettings);

        if ($frameworkConfig === null)
        {
            throw new UsageException('WAutoRebuild добавлен, но WebTestingModule не подключен');
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

}
