<?php

namespace Codeception\Template;

use Codeception\InitTemplate;
use Codeception\Lib\WFramework\Generator\WProjectStructure;
use Codeception\Util\Template;
use Symfony\Component\Yaml\Yaml;

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
        - Codeception\Module\LoggerModule
        - Codeception\Module\WebAssertsModule
        - Codeception\Module\SeleniumServerModule
        - Codeception\Module\ShotsStorageModule:
              source:    'local'
              accessKey: '***accessKey***'
              secretKey: '***secretKey***'
              bucket:    '***bucket***'
        - Codeception\Module\WebTestingModule:
              depends:
                  - Codeception\Module\LoggerModule
                  - Codeception\Module\SeleniumServerModule
                  - Codeception\Module\WebAssertsModule
                  - Codeception\Module\ShotsStorageModule
              elementTimeout: 16               # Таймаут умных ожиданий для PageObject'ов (наследников WBlock, WElement)
              collectionTimeout: 32            # Таймаут умных ожиданий для коллекций PageObject'ов (наследников WCollection)
              autostartSeleniumServer: true    # Автоматически запускать SeleniumServer при старте тестов
              topBarHeight: 100                # При прокрутке viewport к элементу, будет сохранён отступ от верхней границы viewport равный этому числу в пикселях
              holdBrowserOpen: false           # Оставлять браузер запущенным после завершения теста (помогает в дебаге, но забивает память множеством копий веб-драйвера)
              restartBeforeEachTest: true      # Запускать чистую сессию браузера Перед каждым тестом
              debug: true                      # Выводить в консоль полный лог прогона теста, а в HTML-лог добавить больше скриншотов прогона (сильно замедляет прогон тестов)
              takeScreenshots: true            # Делать скриншоты прогона для HTML-лога
              screenshotsToVideo: true         # Преобразовывать скриншоты HTML-лога в видео
              capabilities:
                  build: 'Build XXX'
                  project: '{{project_uppercase}}'
                  name: 'Test Run'
              shotRun: false
              maxDeviation: 10

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

    protected $firstTest = <<<EOF
<?php
class LoginCest 
{    
    public function _before(AcceptanceTester \$I)
    {
        \$I->amOnPage('/');
    }

    public function loginSuccessfully(AcceptanceTester \$I)
    {
        // write a positive login test 
    }
    
    public function loginWithInvalidPassword(AcceptanceTester \$I)
    {
        // write a negative login test
    }       
}
EOF;


    public function setup()
    {
        $this->checkInstalled();

        $this->say("Настраиваем тестовый проект");
        $this->say("");

        $projectName = $this->ask("Задайте короткое название проекта: ", 'ui');

        $projectUCFirst = ucfirst($projectName);
        $projectLowercase = strtolower($projectName);
        $projectUppercase = strtoupper($projectName);

        $this->namespace = $projectLowercase;
        $root = $projectLowercase;

        $this->createEmptyDirectory($outputDir = $root . DIRECTORY_SEPARATOR . '_output');
        $this->createEmptyDirectory($root . DIRECTORY_SEPARATOR . '_data');
        $this->createEmptyDirectory($root . DIRECTORY_SEPARATOR . '_envs');
        $this->createEmptyDirectory($root . DIRECTORY_SEPARATOR . 'Tests');
        $this->createDirectoryFor($supportDir = $root . DIRECTORY_SEPARATOR . '_support');
        $this->createDirectoryFor($generatedDir = $supportDir . DIRECTORY_SEPARATOR . '_generated');
        $this->createDirectoryFor($helperDir = $supportDir . DIRECTORY_SEPARATOR . 'Helper');
        $this->createEmptyDirectory($helperDir . DIRECTORY_SEPARATOR  . 'AliasMaps');
        $this->createDirectoryFor($helperDir . DIRECTORY_SEPARATOR  . 'Blocks');
        $this->createEmptyDirectory($helperDir . DIRECTORY_SEPARATOR  . 'Conditions');
        $this->createDirectoryFor($helperDir . DIRECTORY_SEPARATOR  . 'Elements');
        $this->createEmptyDirectory($helperDir . DIRECTORY_SEPARATOR  . 'Operations');
        $this->createDirectoryFor($helperDir . DIRECTORY_SEPARATOR  . 'Steps');

        $this->gitIgnore($outputDir);
        $this->gitIgnore($generatedDir);

        $this->sayInfo("Структура каталогов проекта успешно создана в директории: $root");


        $actorName = $projectUCFirst . 'Tester';

        $configFile = (new Template($this->configTemplate))
                                ->place('namespace', $this->namespace)
                                ->place('actor_name', $actorName)
                                ->place('project_uppercase', $projectUppercase)
                                ->produce();

        $this->createFile($root . DIRECTORY_SEPARATOR . 'codeception.yml', $configFile);

        $this->sayInfo("Файл конфигурации проекта: codeception.yml - успешно создан");


        $this->createActor($actorName, $supportDir, Yaml::parse($configFile)['suites']['webui']);

        $this->sayInfo("Актор проекта: $actorName - успешно создан");


        (new WProjectStructure($projectUCFirst, $this->namespace, $actorName, $supportDir))->build();

        $this->sayInfo("Хелперы проекта успешно сгенерированы");


//        $this->sayInfo("Created global config codeception.yml inside the root directory");
//        $this->createFile($root . DIRECTORY_SEPARATOR . 'LoginCest.php', $this->firstTest);
//        $this->sayInfo("Created a demo test LoginCest.php");

        $this->say();
        $this->saySuccess("ПРОЕКТ НАСТРОЕН");

//        $this->say();
//        $this->say("<bold>Next steps:</bold>");
//        $this->say('1. Launch Selenium Server and webserver');
//        $this->say("2. Edit <bold>$root/LoginCest.php</bold> to test login of your application");
//        $this->say("3. Run tests using: <comment>codecept run</comment>");
//        $this->say();
//        $this->say("HINT: Add '\\Codeception\\Step\\Retry' trait to AcceptanceTester class to enable auto-retries");
//        $this->say("HINT: See https://codeception.com/docs/03-AcceptanceTests#retry");
//        $this->say("<bold>Happy testing!</bold>");
    }
}
