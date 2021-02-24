<?php


namespace dodge\Helper\Elements\DodgeColorPicker;


use Codeception\Lib\WFramework\AliasMaps\AliasMap;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use dodge\Helper\Collections\DodgeCollection;
use dodge\Helper\Elements\Basic\DodgeLabel;
use dodge\Helper\Elements\DodgeColorPicker\Inner\DodgeColorButton;
use dodge\Helper\Elements\DodgeElement;

class DodgeColorPicker extends DodgeElement
{

    protected function initTypeName() : string
    {
        return "Цветовая панель";
    }

    public function __construct(WFrom $importer)
    {
        $this->currentColorLabel = DodgeLabel::fromXpath('Выбранный цвет', ".//label");
        $this->colors = DodgeCollection::fromFirstElement(DodgeColorButton::fromXpath('Кнопка цвета', ".//div[contains(@class, 'sdp-form-radio ')]"));

        parent::__construct($importer);
    }

    /** @var AliasMap */
    protected $aliasMap;

    public function setColorsAliasMap(AliasMap $aliasMap) : DodgeColorPicker
    {
        $this->aliasMap = $aliasMap;

        return $this;
    }

    public function selectColor(string $alias) : DodgeColorPicker
    {
        $color = $this->aliasMap->getValue($alias);

        WLogger::logInfo($this, "выбираем цвет: $color");

        $colorsMap = $this
                        ->colors
                        ->shouldBeGreaterThanOrEqual(count($this->aliasMap->getAliasesList()))
                        ->getElementsMap('getColorName')
                        ;

        if (!isset($colorsMap[$color]))
        {
            throw new UsageException($this . " -> не содержит цвета: $color - среди доступных цветов: " . implode(', ', array_keys($colorsMap)));
        }

        /** @var DodgeColorButton $colorButton */
        $colorButton = $colorsMap[$color];
        $colorButton->click();

        $this->currentColorLabel->shouldHaveText($color);

        return $this;
    }
}
