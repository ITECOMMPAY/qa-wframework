<?php


namespace dodge\Helper\Elements\DodgeColorPicker;


use Common\Module\WFramework\AliasMap\AliasMap;
use Common\Module\WFramework\Exceptions\Common\UsageException;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;
use Common\Module\WFramework\WebObjects\Primitive\WArray;
use Common\Module\WFramework\WebObjects\Primitive\WLabel;
use dodge\Helper\Elements\DodgeColorPicker\Inner\DodgeColorButton;

class DodgeColorPicker extends WElement
{

    protected function initTypeName() : string
    {
        return "Цветовая панель";
    }

    public function __construct(WFrom $importer)
    {
        $this->currentColorLabel = WLabel::fromXpath('Выбранный цвет', ".//label");
        $this->colors = WArray::fromFirstElement(DodgeColorButton::fromXpath('Кнопка цвета', ".//div[contains(@class, 'sdp-form-radio ')]"));

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

        WLogger::logInfo($this . " -> выбираем цвет: $color");

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
