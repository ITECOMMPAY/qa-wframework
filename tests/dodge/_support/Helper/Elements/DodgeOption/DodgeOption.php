<?php


namespace dodge\Helper\Elements\DodgeOption;


use Codeception\Lib\WFramework\Exceptions\Common\FrameworkStaledException;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Primitive\WButton;
use Codeception\Lib\WFramework\WebObjects\Primitive\WCheckbox;
use Codeception\Lib\WFramework\WebObjects\Primitive\WLabel;

class DodgeOption extends WElement
{
    protected function initTypeName() : string
    {
        return "Опция авто";
    }

    public function __construct(WFrom $importer)
    {
        $this->button    = WButton::fromXpath('Кнопка', ".");
        $this->nameLabel = WLabel::fromXpath('Название опции', ".//label[contains(@title, 'Apply')]");
        $this->_checkbox = WCheckbox::fromXpath('Галочка', ".//input");

        parent::__construct($importer);
    }

    public function getOptionName() : string
    {
        WLogger::logInfo($this . " -> получаем название опции");

        return $this
                    ->nameLabel
                    ->returnSeleniumElement()
                    ->get()
                    ->attribute('data-lid')
                    ;
    }

    public function getOptionPrice() : int
    {
        WLogger::logInfo($this . " -> получаем цену опции");

        $priceText = $this
                        ->nameLabel
                        ->returnSeleniumElement()
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

        return $this->_checkbox->checked();
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
