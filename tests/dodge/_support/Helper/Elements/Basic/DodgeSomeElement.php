<?php


namespace dodge\Helper\Elements\Basic;


use dodge\Helper\Elements\DodgeElement;

class DodgeSomeElement extends DodgeElement
{
    protected function initTypeName() : string
    {
        return "Некий элемент";
    }
}