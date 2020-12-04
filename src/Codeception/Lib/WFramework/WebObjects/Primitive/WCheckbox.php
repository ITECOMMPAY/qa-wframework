<?php


namespace Codeception\Lib\WFramework\WebObjects\Primitive;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Primitive\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Primitive\Interfaces\IMemorizeValue;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\Import\WFrom;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

class WCheckbox extends WElement implements IMemorizeValue, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Флаг';
    }

    public function __construct(WFrom $importer)
    {
        $this->button = WButton::fromXpath('Флаг', ".");

        parent::__construct($importer);
    }

    /**
     * @return WButton
     */
    protected function getButton() : WButton
    {
        return $this->button;
    }

    public function check() : WCheckbox
    {
        WLogger::logInfo($this . " -> ставим");

        if (!$this->checked())
        {
            $this->getButton()->click();
        }

        return $this;
    }

    public function uncheck() : WCheckbox
    {
        WLogger::logInfo($this . " -> снимаем");

        if ($this->checked())
        {
            $this->getButton()->click();
        }

        return $this;
    }

    public function checked() : bool
    {
        WLogger::logInfo($this . " -> проставлен?");

        return $this->getButton()->returnSeleniumElement()->checkIt()->is(Cond::selected());
    }

    public function shouldBeChecked() : WCheckbox
    {
        $this->should(Cond::selected(), 'должен быть проставлен');

        return $this;
    }

    public function shouldBeUnchecked() : WCheckbox
    {
        $this->should(Cond::not(Cond::selected()), 'должен быть снят');

        return $this;
    }

    public function memorizeCurrentValue(string $propertiesKey = '') : WCheckbox
    {
        WLogger::logInfo($this . " -> запоминаем текущее значение");

        $key = empty($propertiesKey) ? (string) $this : $propertiesKey;

        TestProperties::setValue($key, $this->getCurrentValueString());

        return $this;
    }

    public function isHavingMemorizedValue(string $propertiesKey = '') : bool
    {
        WLogger::logInfo($this . " -> имеет запомненное значение?");

        $key = empty($propertiesKey) ? (string) $this : $propertiesKey;

        $expectedValue = TestProperties::mustGetValue($key);

        $actualValue = $this->getCurrentValueString();

        WLogger::logDebug('Ожидаемое значение: ' . $expectedValue . PHP_EOL . ' - актуальное значение: ' . $actualValue);

        return $expectedValue === $actualValue;
    }

    public function getCurrentValueString() : string
    {
        return json_encode($this->checked());
    }
}
