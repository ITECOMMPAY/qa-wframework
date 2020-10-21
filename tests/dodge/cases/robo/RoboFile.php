<?php

use Common\Module\WFramework\WebObjects\Verifier\PageObjectsVerifier;
use Symfony\Component\Yaml\Yaml;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function parallelSelfCheck()
    {
        $parallel = $this->taskParallelExec();

        $env = 'dodge-loc,dodge-loc-chrome,dodge-loc-1920';

        $codeceptConfig = realpath(__DIR__ . '/../../codeception.yml');

        $config = Yaml::parse(file_get_contents($codeceptConfig));
        $codeceptOutputDir = realpath(dirname($codeceptConfig) . '/' . $config['paths']['output']);

        echo "env: $env" . PHP_EOL;
        echo "config: $codeceptConfig" . PHP_EOL;
        echo "output: $codeceptOutputDir" . PHP_EOL;

        $groups = ['self_check_thread_1', 'self_check_thread_2', 'self_check_thread_3', 'self_check_thread_4'];

        foreach ($groups as $group)
        {
            $parallel->process(
                $this->taskCodecept()
                     ->configFile($codeceptConfig)
                     ->suite('cases')
                     ->env($env)
                     ->group($group)
            );
        }

        $runResult = $parallel->run();

        $outputs = [];

        foreach ($groups as $group)
        {
            $filename = "$codeceptOutputDir/$group.json";

            if (file_exists($filename))
            {
                $outputs[] = json_decode(file_get_contents($filename), true);
            }
        }

        $outputMerged = array_merge_recursive(...$outputs);

        PageObjectsVerifier::printResult($outputMerged);

        return $runResult;
    }
}
