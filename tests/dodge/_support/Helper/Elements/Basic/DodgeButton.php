<?php


namespace dodge\Helper\Elements\Basic;


use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IClickable;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IHaveReadableText;
use dodge\Helper\Elements\DodgeElement;

class DodgeButton extends DodgeElement implements IClickable, IHaveReadableText
{
    protected function initTypeName() : string
    {
        return 'Кнопка';
    }

    public function click() : DodgeButton
    {
        $this
            ->returnOperations()
            ->mouse()
            ->clickSmart()
            ;

        return $this;
    }

    public function clickMouseDown() : DodgeButton
    {
        $this
            ->returnOperations()
            ->mouse()
            ->clickWithLeftButton()
            ;

        return $this;
    }

    public function getFilteredText(string $regex) : string
    {
        return $this
                    ->returnOperations()
                    ->get()
                    ->textFiltered($regex)
                    ;
    }
}