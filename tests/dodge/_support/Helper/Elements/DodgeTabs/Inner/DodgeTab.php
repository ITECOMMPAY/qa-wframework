<?php


namespace dodge\Helper\Elements\DodgeTabs\Inner;


use dodge\Helper\Elements\Basic\DodgeButton;

class DodgeTab extends DodgeButton
{
    protected function initTypeName() : string
    {
        return "Вкладка";
    }

    public function isSelected() : bool
    {
        return $this->is(new TabSelected());
    }

    public function finallySelected() : bool
    {
        return $this->finally_(new TabSelected());
    }

    public function shouldBeSelected() : DodgeTab
    {
        return $this->should(new TabSelected());
    }
}
