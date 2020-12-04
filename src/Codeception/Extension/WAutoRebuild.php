<?php


namespace Codeception\Extension;


use Codeception\Configuration;
use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Lib\WFramework\Generator\WProjectStructure;

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

        $supportDir = Configuration::supportDir();
        $suiteSettings = $e->getSettings();
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

}
