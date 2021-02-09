<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\Conditions\Not_;
use Codeception\Lib\WFramework\Conditions\TextEmpty;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveWritableText;
use dodge\Helper\Elements\DodgeElement;

class DodgeTextBox extends DodgeElement implements IHaveWritableText, IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Поле ввода';
    }

    public function set(string $text) : DodgeTextBox
    {
        WLogger::logAction($this, "задаём текст: $text");

        $this
            ->returnOperations()
            ->field()
            ->set($text, 0)
            ;

        return $this;
    }

    public function append(string $text) : DodgeTextBox
    {
        WLogger::logAction($this, "дописываем в конец: $text");

        $this
            ->returnOperations()
            ->field()
            ->append($text, 0)
            ;

        return $this;
    }

    public function prepend(string $text) : DodgeTextBox
    {
        WLogger::logAction($this, "дописываем в начало: $text");

        $this
            ->returnOperations()
            ->field()
            ->prepend($text, 0)
            ;

        return $this;
    }

    public function clear() : DodgeTextBox
    {
        WLogger::logAction($this, "очищаем");

        $this
            ->returnOperations()
            ->field()
            ->clear(0)
            ;

        return $this;
    }

    public function isEmpty() : bool
    {
        return $this->is(new TextEmpty());
    }

    public function isNotEmpty() : bool
    {
        return $this->is(new Not_(new TextEmpty()));
    }

    public function shouldBeEmpty() : DodgeTextBox
    {
        return $this->should(new TextEmpty());
    }

    public function shouldNotBeEmpty() : DodgeTextBox
    {
        return $this->should(new Not_(new TextEmpty()));
    }

    public function finallyEmpty() : bool
    {
        return $this->finally_(new TextEmpty());
    }

    public function finallyNotEmpty() : bool
    {
        return $this->finally_(new Not_(new TextEmpty()));
    }

    public function getCurrentValueString() : string
    {
        return $this->getAllText();
    }
}