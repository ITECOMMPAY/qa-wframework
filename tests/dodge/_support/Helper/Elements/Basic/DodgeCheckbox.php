<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\Conditions\Not_;
use Codeception\Lib\WFramework\Conditions\Selected;
use Codeception\Lib\WFramework\Logger\WLogger;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveCurrentValue;
use dodge\Helper\Elements\DodgeElement;

class DodgeCheckbox extends DodgeElement implements IHaveCurrentValue
{
    protected function initTypeName() : string
    {
        return 'Флаг';
    }

    public function check() : DodgeCheckbox
    {
        WLogger::logInfo($this, "ставим");

        if ($this->isUnchecked())
        {
            $this
                ->returnOperations()
                ->mouse()
                ->clickSmart()
                ;
        }

        return $this;
    }

    public function uncheck() : DodgeCheckbox
    {
        WLogger::logInfo($this, "снимаем");

        if ($this->isChecked())
        {
            $this
                ->returnOperations()
                ->mouse()
                ->clickSmart()
                ;
        }

        return $this;
    }

    public function isChecked() : bool
    {
        return $this->is(new Selected());
    }

    public function isUnchecked() : bool
    {
        return $this->is(new Not_(new Selected()));
    }

    public function shouldBeChecked() : DodgeCheckbox
    {
        return $this->should(new Selected());
    }

    public function shouldBeUnchecked() : DodgeCheckbox
    {
        return $this->should(new Not_(new Selected()));
    }

    public function finallyChecked() : bool
    {
        return $this->finally_(new Selected());
    }

    public function finallyUnchecked() : bool
    {
        return $this->finally_(new Not_(new Selected()));
    }

    public function getCurrentValueString() : string
    {
        return json_encode($this->isChecked());
    }
}