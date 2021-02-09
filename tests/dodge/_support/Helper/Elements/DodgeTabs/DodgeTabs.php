<?php


namespace dodge\Helper\Elements\DodgeTabs;

use Codeception\Lib\WFramework\AliasMaps\AliasMap;
use Codeception\Lib\WFramework\AliasMaps\EmptyAliasMap;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use dodge\Helper\Collections\DodgeCollection;
use dodge\Helper\Elements\DodgeElement;
use dodge\Helper\Elements\DodgeTabs\Inner\DodgeTab;

/**
 * Class DodgeTabs
 *
 * Вкладки.
 *
 * Для работы нужно задать AliasMap заголовков с помощью метода setHeadersAliasMap().
 *
 * @package dodge\Helper\Elements\DodgeTabs
 */
class DodgeTabs extends DodgeElement
{
    protected function initTypeName() : string
    {
        return "Вкладки";
    }

    /** @var AliasMap */
    protected $headersAliasMap;

    public function __construct(WFrom $importer)
    {
        $this->headersAliasMap = new EmptyAliasMap();

        $this->tabs = DodgeCollection::fromFirstElement(DodgeTab::fromXpath('Вкладка', ".//li"));

        parent::__construct($importer);
    }

    public function setHeadersAliasMap(AliasMap $aliasMap) : DodgeTabs
    {
        $this->headersAliasMap = $aliasMap;

        return $this;
    }


    public function selectTab(string $alias) : DodgeTabs
    {
        $tabText = $this->headersAliasMap->getValue($alias);

        WLogger::logInfo($this, "выбираем вкладку: $tabText");

        /** @var DodgeTab $tab */
        $tab = $this->getTab($alias);
        $tab->click();
        $tab->shouldBeSelected();

        TestProperties::setValue('lastSelectedTab', $tabText);

        return $this;
    }

    public function getTab(string $alias) : DodgeTab
    {
        $tabText = $this->headersAliasMap->getValue($alias);

        WLogger::logInfo($this, "получаем вкладку: $tabText");

        $tabs = $this
                    ->tabs
                    ->shouldExist()
                    ->getElementsMap('getAllText')
                    ;

        if (!isset($tabs[$tabText]))
        {
            throw new UsageException($this . " -> не содержит вкладки с заголовком: $tabText");
        }

        /** @var DodgeTab $tab */
        $tab = $tabs[$tabText];

        return $tab;
    }
}
