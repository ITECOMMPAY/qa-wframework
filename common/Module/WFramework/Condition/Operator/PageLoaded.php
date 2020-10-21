<?php


namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;

class PageLoaded extends Cond
{
    const SCRIPT = "return document.readyState;";

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $readyState = $facadeWebElement
                                    ->exec()
                                    ->script(static::SCRIPT)
                                    ;

        $this->result = $readyState === 'complete';
    }

    public function printExpectedValue() : string
    {
        return 'страница загрузилась';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'страница загрузилась' : 'страница НЕ загрузилась' ;
    }
}
