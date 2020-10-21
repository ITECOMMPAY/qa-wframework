<?php


namespace dodge\Helper\Elements\DodgeTabs\Inner;


use Common\Module\WFramework\WebObjects\Primitive\WButton;

class DodgeTab extends WButton
{
    protected function initTypeName() : string
    {
        return "Вкладка";
    }

    public function isSelected() : bool
    {
        return $this->is(TabSelected::new(), 'вкладка выбрана?');
    }

    public function shouldBeSelected() : DodgeTab
    {
        $this->should(TabSelected::new(), 'вкладка должна быть выбрана');

        return $this;
    }
}
