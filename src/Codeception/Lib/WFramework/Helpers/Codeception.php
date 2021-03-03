<?php


namespace Codeception\Lib\WFramework\Helpers;


class Codeception
{
    public static function getModuleConfig(string $name, array $suiteSettings) : ?array
    {
        $enabledModules = $suiteSettings['modules']['enabled'];

        foreach ($enabledModules as $module)
        {
            $moduleName = $module;
            $moduleConfig = [];

            if (is_array($module))
            {
                $moduleConfig = reset($module);
                $moduleName   = key($module);
            }

            if (!is_string($moduleName))
            {
                continue;
            }

            if (strpos($moduleName, $name) !== false)
            {
                return $moduleConfig;
            }
        }

        return null;
    }
}