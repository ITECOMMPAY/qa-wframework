<?php
/**
 * Created by PhpStorm.
 * User: egor
 * Date: 14.03.19
 * Time: 15:43
 */

namespace Common\Module\WFramework\CollectionCondition\Operator;


use Common\Module\WFramework\CollectionCondition\CCond;
use Common\Module\WFramework\Condition\Cond;
use Common\Module\WFramework\FacadeWebElements\FacadeWebElements;
use function implode;


class ExactTexts extends CCond
{
    protected function apply(FacadeWebElements $facadeWebElements)
    {
        if ($facadeWebElements
                                    ->checkIt()
                                    ->doesNotHave(
                                            CCond::sizeGreaterThanOrEqual(
                                                    count($this->expectedValue))))
        {
            $this->actualValue = null;
            $this->result = False;
            return;
        }

        $elementsCount = count($facadeWebElements);

        $this->actualValue = $facadeWebElements
                                                        ->get()
                                                        ->texts()
                                                        ;

        for ($i = 0; $i < $elementsCount; $i++)
        {
            $element = $facadeWebElements->getElementsArray()[$i];

            if ($element
                    ->checkIt()
                    ->doesNotHave(
                        Cond::exactText(
                            $this->expectedValue[$i])))
            {
                $this->result = False;
                return;
            }
        }

        $this->result = True;
    }

    public function __construct(string $conditionName, ...$text)
    {
        parent::__construct($conditionName);

        $this->expectedValue = $text;
    }

    public function printExpectedValue() : string
    {
        return 'элементы в коллекции должны точно иметь следующие строки, в заданном порядке: ' . implode(', ', $this->expectedValue);
    }

    public function printActualValue() : string
    {
        if ($this->actualValue === null)
        {
            return 'количество элементов в коллекции меньше чем количество проверяемых строк';
        }

        return 'элементы в коллекции имеют следующие строки: ' . implode(', ', $this->actualValue);
    }
}
