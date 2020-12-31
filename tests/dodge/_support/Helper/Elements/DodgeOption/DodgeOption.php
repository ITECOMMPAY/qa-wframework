<?php


namespace dodge\Helper\Elements\DodgeOption;


use Codeception\Lib\WFramework\Exceptions\FrameworkStaledException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use dodge\Helper\Elements\Basic\DodgeButton;
use dodge\Helper\Elements\Basic\DodgeCheckbox;
use dodge\Helper\Elements\Basic\DodgeLabel;
use dodge\Helper\Elements\DodgeElement;

class DodgeOption extends DodgeElement
{
    protected function initTypeName() : string
    {
        return "Опция авто";
    }

    public function __construct(WFrom $importer)
    {
        $this->button    = DodgeButton::fromXpath('Кнопка', ".");
        $this->nameLabel = DodgeLabel::fromXpath('Название опции', ".//label[contains(@title, 'Apply')]");
        $this->_checkbox = DodgeCheckbox::fromXpath('Галочка', ".//input");

        parent::__construct($importer);
    }

    public function getOptionName() : string
    {
        WLogger::logInfo($this . " -> получаем название опции");

        return $this
                    ->nameLabel
                    ->returnOperations()
                    ->get()
                    ->attribute('data-lid')
                    ;
    }

    public function getOptionPrice() : int
    {
        WLogger::logInfo($this . " -> получаем цену опции");

        $priceText = $this
                        ->nameLabel
                        ->returnOperations()
                        ->get()
                        ->attribute('title')
                        ;

        if (preg_match('%Price:\s+\$(?\'price\'\d+)%m', $priceText, $matches) !== 1)
        {
            throw new FrameworkStaledException('Не получилось распарсить цену из строки: ' . $priceText);
        }

        return $matches['price'];
    }

    public function selected() : bool
    {
        WLogger::logInfo($this . " -> выбрана?");

        return $this->_checkbox->isChecked();
    }

    public function select() : DodgeOption
    {
        WLogger::logInfo($this . " -> выбираем?");

        if ($this->selected())
        {
            return $this;
        }

        $this->button->click();

        $this->_checkbox->shouldBeChecked();

        return $this;
    }
}
