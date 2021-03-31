<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\ExampleNodes\Tests\SelfCheckCestNode;
use Codeception\Util\Template;

class SelfCheckCestSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use Codeception\Lib\WFramework\WebObjects\Verifier\PageObjectsVerifier;
use {{steps_class_full}};
use {{actor_class_full}};

class selfCheckCest
{
    protected string $pageObjectsSubDir = '_support/Helper/Blocks/';

    protected array $ignoredPageObjects = [

    ];

    protected bool $takeScreenshots = true; // Если в true - то для каждого успешно открытого PO, будет сохранён скриншот

    /**
     * Этот тест проверяет, что все локаторы всех PageObject'ов (кроме тех, что указаны в $ignoredPageObjects) - валидные
     *
     * ./vendor/bin/codecept run webui selfCheckCest:selfCheckAll -c {{codeception_config_subdir}} --env loc-base,loc-bro-chrome,loc-res-1920
     *
     * @param {{actor_class_short}} $I
     * @param {{steps_class_short}} $steps
     * @throws \Codeception\Lib\WFramework\Exceptions\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function selfCheckAll({{actor_class_short}} $I, {{steps_class_short}} $steps) : void
    {
        $I->wantToTest('Все PageObject\'ы имеют валидные локаторы');

        $pageObjectDir = codecept_root_dir() . $this->pageObjectsSubDir;

        $verifier = new PageObjectsVerifier($I, $pageObjectDir, $this->ignoredPageObjects, $this->takeScreenshots);
        $verifier->checkPageObjects();
        $verifier::printResult($verifier->getResult());

        $I->assertEmpty($verifier->getResult());
    }

    /**
     * Этот тест проверяет, что локаторы заданного PageObject'а - валидные
     *
     * ./vendor/bin/codecept run webui selfCheckCest:checkPageObject -c {{codeception_config_subdir}} --env loc-base,loc-bro-chrome,loc-res-1920
     *
     * @param {{actor_class_short}} $I
     * @param {{steps_class_short}} $steps
     * @throws \Codeception\Lib\WFramework\Exceptions\UsageException
     * @throws \ImagickException
     * @throws \ReflectionException
     */
    public function checkPageObject({{actor_class_short}} $I, {{steps_class_short}} $steps) : void
    {
        $I->wantToTest('Указанный PageObject имеет валидные локаторы');

        // Полное имя класса PO, который нужно валидировать
        // (правой кнопкой по имени класса -> Copy/Paste Special -> Copy Reference)
        $pageObject = '{{login_block_class_full}}';

        $pageObjectDir = codecept_root_dir() . $this->pageObjectsSubDir;

        $verifier = new PageObjectsVerifier($I, $pageObjectDir, $this->ignoredPageObjects, $this->takeScreenshots);
        $verifier->checkPageObject($pageObject);
        $verifier::printResult($verifier->getResult());

        $I->assertEmpty($verifier->getResult());
    }
}

EOF;

    protected SelfCheckCestNode $node;

    public function __construct(SelfCheckCestNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                            ->place('namespace',                 $this->node->getOutputNamespace())
                            ->place('codeception_config_subdir', $this->node->getRootNode()->getCodeceptionConfigSubdir())
                            ->place('actor_class_full',          $this->node->getRootNode()->getActorClassFull())
                            ->place('actor_class_short',         $this->node->getRootNode()->getActorClassShort())
                            ->place('steps_class_full',          $this->node->getStepsNode()->getEntityClassFull())
                            ->place('steps_class_short',         $this->node->getStepsNode()->getEntityClassShort())
                            ->place('login_block_class_full',    $this->node->getLoginBlockNode()->getEntityClassFull())
                            ->produce();

        $this->node->setSource($source);
    }
}