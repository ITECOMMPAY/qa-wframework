<?php


namespace dodge\Helper\Elements\DodgeTabs\Inner;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;

class TabSelected extends AbstractCondition
{
    public function getName() : string
    {
        return "выбрана?";
    }

    public function acceptDodgeTab(DodgeTab $tab) : bool
    {
        $class = $tab->returnOperations()->get()->attribute('class') ?? '';

        return stripos($class, 'active') !== false;
    }
}
