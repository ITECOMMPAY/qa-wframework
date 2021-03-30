<?php

namespace Codeception\Template;

use Codeception\InitTemplate;
use Codeception\Lib\WFramework\Generator\WProjectStructure;
use Codeception\Util\Template;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class WProject
 *
 * Генерация нового проекта.
 * Запускать так: ./vendor/bin/codecept init --path ./tests WProject
 *
 * @package Codeception\Template
 */
class WProject extends InitTemplate
{
    protected $configTemplate = <<<EOF
namespace: {{namespace}}

suites:
    webui:
        actor: {{actor_name}}
        path: .


modules:
    enabled:
        -   Codeception\Module\WebTestingModule:
                url: '{{url}}'
                email: 'some@email.com'
                password: '123456'
                browser: chrome
                window_size: "1920x1080"
                elementTimeout:          16      # Таймаут умных ожиданий для PageObject'ов (наследников WBlock, WElement)
                collectionTimeout:       32      # Таймаут умных ожиданий для коллекций PageObject'ов (наследников WCollection)
                autostartSeleniumServer: true    # Автоматически запускать SeleniumServer при старте тестов
                topBarHeight:            100     # При прокрутке viewport к элементу, будет сохранён отступ от верхней границы viewport равный этому числу в пикселях
                holdBrowserOpen:         false   # Оставлять браузер запущенным после завершения теста (помогает в дебаге, но забивает память множеством копий веб-драйвера)
                restartBeforeEachTest:   true    # Запускать чистую сессию браузера Перед каждым тестом
                capabilities:
                    build:   'Build XXX'
                    project: '{{project_uppercase}}'
                    name:    'Test Run'
                shotRun:                 false
                maxDeviation:            10
        - Codeception\Module\FFmpegManagerModule
        -   Codeception\Module\HtmlLoggerModule:
                depends:
                    - Codeception\Module\WebTestingModule
                    - Codeception\Module\FFmpegManagerModule
                    
                debug:                   true    # (!!! сильно замедляет прогон тестов !!!) Выводить в консоль полный лог прогона теста, а в HTML-лог добавить больше скриншотов прогона
                takeScreenshots:         true    # Делать скриншоты прогона для HTML-лога
                screenshotsToVideo:      true    # Преобразовывать скриншоты HTML-лога в видео
        -   Codeception\Module\SeleniumServerModule:
                depends:
                    - Codeception\Module\WebTestingModule
                    
                autoUpdateDrivers:       true    # Автоматически скачивать новые драйвера
                sessionTimeout:          3600    # Максимальное время на одну тестовую сессию
                port:                    4444    # По-дефолту сервер Селениума стартует на 4444 порту
        - Codeception\Module\WebAssertsModule
        -   Codeception\Module\ShotsStorageModule:
                source:                  'local'
                accessKey:               '***accessKey***'
                secretKey:               '***secretKey***'
                bucket:                  '***bucket***'




extensions:
    enabled:
        - Codeception\Extension\WAutoRebuild
    commands:
        - Codeception\Command\WBuild

paths:
    tests: Tests
    output: _output
    data: _data
    support: _support
    envs: _envs

settings:
    memory_limit: 4096M

EOF;

    public function setup()
    {
        $this->checkInstalled();

        $this->say("Настраиваем тестовый проект");
        $this->say("");

        while(true)
        {
            $projectName = $this->ask("Задайте короткое название проекта (<=5 символов латиницы): ", 'ui');

            if (strlen($projectName) <= 5 && preg_match('%^[[:alpha:]][[:alnum:]]*$%', $projectName))
            {
                break;
            }
        }

        $url = $this->ask("Задайте адрес сайта: ", 'https://colorlib.com/etc/lf/Login_v1/index.html');

        $projectUCFirst = ucfirst($projectName);
        $projectLowercase = strtolower($projectName);
        $projectUppercase = strtoupper($projectName);

        $this->namespace = $projectLowercase;
        $root = $projectLowercase;

        $this->createEmptyDirectory($outputDir =  $root . DIRECTORY_SEPARATOR . '_output');
        $this->createEmptyDirectory(          $root . DIRECTORY_SEPARATOR . '_data');
        $this->createEmptyDirectory($envsDir =    $root . DIRECTORY_SEPARATOR . '_envs');
        $this->createEmptyDirectory($testsDir =   $root . DIRECTORY_SEPARATOR . 'Tests');
        $this->createDirectoryFor($supportDir =   $root . DIRECTORY_SEPARATOR . '_support');
        $this->createDirectoryFor($generatedDir = $supportDir . DIRECTORY_SEPARATOR . '_generated');
        $this->createDirectoryFor($helperDir =    $supportDir . DIRECTORY_SEPARATOR . 'Helper');
        $this->createEmptyDirectory(          $helperDir . DIRECTORY_SEPARATOR  . 'AliasMaps');
        $this->createDirectoryFor(        $helperDir . DIRECTORY_SEPARATOR  . 'Blocks');
        $this->createEmptyDirectory(          $helperDir . DIRECTORY_SEPARATOR  . 'Conditions');
        $this->createDirectoryFor(        $helperDir . DIRECTORY_SEPARATOR  . 'Elements');
        $this->createEmptyDirectory(          $helperDir . DIRECTORY_SEPARATOR  . 'Operations');
        $this->createDirectoryFor(        $helperDir . DIRECTORY_SEPARATOR  . 'Steps');

        $this->gitIgnore($outputDir);
        $this->gitIgnore($generatedDir);

        $this->sayInfo("Структура каталогов проекта успешно создана в директории: $root");


        $actorName = $projectUCFirst . 'Tester';

        $configFile = (new Template($this->configTemplate))
                                ->place('namespace', $this->namespace)
                                ->place('actor_name', $actorName)
                                ->place('url', $url)
                                ->place('project_uppercase', $projectUppercase)
                                ->produce();

        $this->createFile($root . DIRECTORY_SEPARATOR . 'codeception.yml', $configFile);

        $this->sayInfo("Файл конфигурации проекта: codeception.yml - успешно создан");


        $this->createActor($actorName, $supportDir, Yaml::parse($configFile)['suites']['webui']);

        $this->sayInfo("Актор проекта: $actorName - успешно создан");

        $projectFullDir = getcwd() . '/' . $root;
        $supportFullDir = getcwd() . '/' . $supportDir;
        $testFullDir    = getcwd() . '/' . $testsDir;

        (new WProjectStructure($projectUCFirst, $actorName, $this->namespace, $projectFullDir, $supportFullDir, $testFullDir, [],true))->build();

        $this->sayInfo("Хелперы проекта успешно сгенерированы");

        $directory = new RecursiveDirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . '_envs');
        $iterator = new RecursiveIteratorIterator($directory);
        $ymlFiles = new RegexIterator($iterator, '/^.+\.yml$/i', RecursiveRegexIterator::GET_MATCH);

        $targetEnvDir = getcwd() . '/' .$envsDir;

        foreach ($ymlFiles as $ymlFile)
        {
            copy($ymlFile[0], $targetEnvDir . DIRECTORY_SEPARATOR . basename($ymlFile[0]));
        }

        $this->sayInfo("env-файлы успешно скопированы");

        $this->say();
        $this->saySuccess("ПРОЕКТ НАСТРОЕН");
    }

    protected function gitIgnore($path)
    {
        $this->createFile($path . DIRECTORY_SEPARATOR . '.gitignore', '**');
    }
}
