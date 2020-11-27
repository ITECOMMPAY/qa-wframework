<?php


namespace Common\Module\WFramework\Extension;


use Codeception\Configuration;
use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Common\Module\WFramework\Generator\WProjectStructure;

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
        $actorSuffix = $this->getGlobalConfig()['actor_suffix'] ?? 'Tester';
        $namespace = $suiteSettings['namespace'] ?? '';

        (new WProjectStructure($actorNameShort, $actorSuffix, $supportDir, $namespace))->build();
    }

}
