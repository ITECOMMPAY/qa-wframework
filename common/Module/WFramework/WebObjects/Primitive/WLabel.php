<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 05.03.19
 * Time: 13:33
 */

namespace Common\Module\WFramework\WebObjects\Primitive;


use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IHaveCurrentValue;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IMemorizeValue;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IHaveReadableText;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;

class WLabel extends WElement implements IHaveReadableText, IMemorizeValue, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Текстовый элемент';
    }

    public function getFilteredText(string $regex) : string
    {
        WLogger::logInfo($this . " -> получаем значение отфильтрованное по регулярке: $regex");

        return $this
                    ->returnSeleniumElement()
                    ->get()
                    ->filteredText($regex)
                    ;
    }

    public function memorizeCurrentValue(string $propertiesKey = '') : WLabel
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
        return $this->getAllText();
    }
}
