<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 01.03.19
 * Time: 17:25
 */

namespace Common\Module\WFramework\WebObjects\Primitive;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\Logger\WLogger;
use Common\Module\WFramework\Properties\TestProperties;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IHaveCurrentValue;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IMemorizeValue;
use Common\Module\WFramework\WebObjects\Primitive\Interfaces\IHaveWritableText;
use Common\Module\WFramework\WebObjects\Base\WElement\WElement;

class WTextBox extends WElement implements IHaveWritableText, IMemorizeValue, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Поле ввода';
    }

    public function set(string $text)
    {
        WLogger::logInfo($this . " -> задаём текст: $text");

        $this
            ->returnSeleniumElement()
            ->mouse()
            ->scrollTo()
            ->clickWithLeftButton()
            ->then()
            ->wait()
            ->forSixteenthTimeout()
            ->then()
            ->field()
            ->clear()
            ->set($text)
            ;

        return $this;
    }

    public function append(string $text)
    {
        WLogger::logInfo($this . " -> добавляем текст: $text");

        $this
            ->returnSeleniumElement()
            ->mouse()
            ->scrollTo()
            ->clickWithLeftButton()
            ->then()
            ->wait()
            ->forSixteenthTimeout()
            ->then()
            ->field()
            ->append($text)
            ;

        return $this;
    }

    public function clear()
    {
        WLogger::logInfo($this . " -> очищаем текст");

        $this
            ->returnSeleniumElement()
            ->field()
            ->clear()
            ;

        return $this;
    }

    /**
     * @return static
     */
    public function shouldBeEmpty()
    {
        return $this->should(Cond::empty(), 'должен быть пустым');
    }

    public function memorizeCurrentValue(string $propertiesKey = '') : WTextBox
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
