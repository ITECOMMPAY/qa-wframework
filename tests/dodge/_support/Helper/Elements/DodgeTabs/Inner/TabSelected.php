<?php


namespace dodge\Helper\Elements\DodgeTabs\Inner;


use Codeception\Lib\WFramework\Condition\Cond;
use Codeception\Lib\WFramework\FacadeWebElement\FacadeWebElement;

class TabSelected extends Cond
{
    public static function new() : TabSelected
    {
        return new self('вкладка выбрана');
    }

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $class = $facadeWebElement->get()->attribute('class') ?? '';

        $this->result = stripos($class, 'active') !== false;
    }

    public function printExpectedValue() : string
    {
        return 'должна быть выбрана';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'выбрана' : 'не выбрана' ;
    }
}
