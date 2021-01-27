<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Explanations\Formatters\TraverseFromRootExplanationFormatter;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\TraverseFromRootExplanationResult;
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
        return "почему он или его родители " . ($this->actualValue ? '' : 'НЕ ') . $this->condition;
    }

    public function acceptWElement($element) : AbstractExplanationResult
    {
        return $this->apply($element);
    }

    public function acceptWBlock($block) : AbstractExplanationResult
    {
        return $this->apply($block);
    }

    public function acceptWCollection($collection) : AbstractExplanationResult
    {
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : AbstractExplanationResult
    {
        $result = new TraverseFromRootExplanationResult();

        $checkResult = $this->actualValue;

        /** @var IPageObject $parentOrSelf */
        foreach ($pageObject->traverseFromRoot() as $parentOrSelf)
        {
            $existCondition = new Exist();
            $checkResult = $parentOrSelf->accept($existCondition);
            $result->addNext($parentOrSelf, $existCondition, $checkResult);

            $checkResult = $parentOrSelf->accept($this->condition);
            $result->addNext($parentOrSelf, $this->condition, $checkResult);

            if ($checkResult === false) //Дошли до первого невалидного элемента - дальше идти смысла нет
            {
                break;
            }
        }

        $result->setProblemNotFound($checkResult !== $this->actualValue);

        return $result;
    }
}