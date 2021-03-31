<?php


namespace Codeception\Lib\WFramework\Generator\SourceGenerator\ExampleEntities\Tests;


use Codeception\Lib\WFramework\Generator\IGenerator;
use Codeception\Lib\WFramework\Generator\ParsingTree\BaseNodes\TestExampleNode;
use Codeception\Util\Template;

class StoreShotsCestSource implements IGenerator
{
    protected const TEMPLATE = <<<'EOF'
<?php


namespace {{namespace}};


use {{actor_class_full}};

class storeShotsCest
{
    /**
     * Перемещает все скриншоты из каталога /_data/shots/temp в каталог /_data/shots
     *
     * Руками это сделать сложнее т.к. каждый скриншот содержит в названии MD5 сумму своего содержимого.
     *
     * @param {{actor_class_short}} $I
     */
    public function acceptTemp({{actor_class_short}} $I)
    {
        $I->wantTo('Принять скриншоты из каталога temp');

        $I->acceptTempShots();
    }

    /**
     * Загружает все скриншоты из каталога /_data/shots в S3
     *
     * @param {{actor_class_short}} $I
     */
    public function uploadShots({{actor_class_short}} $I)
    {
        $I->wantTo('Загрузить скриншоты в S3');

        $I->uploadShots();
    }
}

EOF;

    protected TestExampleNode $node;

    public function __construct(TestExampleNode $node)
    {
        $this->node = $node;
    }

    public function generate() : void
    {
        $source = (new Template(static::TEMPLATE))
                            ->place('namespace',              $this->node->getOutputNamespace())
                            ->place('actor_class_full',       $this->node->getRootNode()->getActorClassFull())
                            ->place('actor_class_short',      $this->node->getRootNode()->getActorClassShort())
                            ->produce();

        $this->node->setSource($source);
    }
}