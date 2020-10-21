<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 26.02.19
 * Time: 16:45
 */

namespace Common\Module\WFramework\Condition\Operator;


use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElement\FacadeWebElement;


class CssClass extends Cond
{
    protected $className = '';

    protected function apply(FacadeWebElement $facadeWebElement)
    {
        $classes = $facadeWebElement
                                    ->get()
                                    ->attribute('class') ?? ''
                                    ;

        $classes = explode(' ', $classes);

        $this->result = in_array($this->className, $classes, true);
    }

    public function __construct(string $conditionName, string $className)
    {
        parent::__construct($conditionName);

        $this->className = $className;
    }

    public function printExpectedValue() : string
    {
        return 'элемент должен иметь заданный CSS класс';
    }

    public function printActualValue() : string
    {
        return $this->result ? 'элемент имеет заданный CSS класс' : 'элемент не имеет заданный CSS класс';
    }
}
