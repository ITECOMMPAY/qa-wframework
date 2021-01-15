<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Explanations\Formatters\TraverseFromRootExplanationFormatter;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResult;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;

/**
 * Class TraverseFromRootExplanation
 *
 * Идёт от корня дерева PageObject'ов до заданного PageObject'а и проверяет для каждого из них
 * выполнение условия
 *
 * @package Codeception\Lib\WFramework\Explanations
 */
class TraverseFromRootExplanation extends AbstractExplanation
{
    public function getName() : string
    {
        return "почему " . ($this->actualValue ? '' : 'НЕ ') . $this->condition;
    }

    public function acceptWElement($element) : ExplanationResult
    {
        return $this->apply($element);
    }

    public function acceptWBlock($block) : ExplanationResult
    {
        return $this->apply($block);
    }

    public function acceptWCollection($collection) : ExplanationResult
    {
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : ExplanationResult
    {
        $checkResult = $this->actualValue;
        $formatter = new TraverseFromRootExplanationFormatter();

        /** @var IPageObject $parentOrSelf */
        foreach ($pageObject->traverseFromRoot() as $parentOrSelf)
        {
            if (!$this->condition instanceof Exist) // Сначала проверяем что элемент вообще присутствует в коде страницы
            {
                $existCondition = new Exist();
                $checkResult = $parentOrSelf->accept($existCondition);
                $formatter->addNext($parentOrSelf, $existCondition, $checkResult);
            }

            $checkResult = $parentOrSelf->accept($this->condition); // А затем и само условие
            $formatter->addNext($parentOrSelf, $this->condition, $checkResult);

            if ($checkResult === false) //Дошли до первого невалидного элемента - дальше идти смысла нет
            {
                break;
            }
        }

        $message = $formatter->getMessage();

        if ($checkResult !== $this->actualValue)
        {
            $message .= $formatter::EXPLANATIONS_DELIMITER;
            $message .= 'ПРОБЛЕМА НЕ ВЫЯВЛЕНА! ' . PHP_EOL;
            $message .= '                            возможно в тесте отсутствует умное ожидание перед операцией' . PHP_EOL;
        }

        return new ExplanationResult($message);
    }
}