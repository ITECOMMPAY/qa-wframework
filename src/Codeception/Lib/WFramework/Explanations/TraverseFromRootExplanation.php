<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\TraverseFromRootExplanationResult;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

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
        return "почему он или его родители " . ($this->conditionResult ? '' : 'НЕ ') . $this->condition;
    }

    public function acceptWElement(WElement $element) : AbstractExplanationResult
    {
        return $this->apply($element);
    }

    public function acceptWBlock(WBlock $block) : AbstractExplanationResult
    {
        return $this->apply($block);
    }

    public function acceptWCollection(WCollection $collection) : AbstractExplanationResult
    {
        return $this->apply($collection);
    }

    protected function apply(IPageObject $pageObject) : AbstractExplanationResult
    {
        $result = new TraverseFromRootExplanationResult();

        $checkResult = $this->conditionResult;

        /** @var IPageObject $parentOrSelf */
        foreach ($pageObject->traverseFromRoot() as $parentOrSelf)
        {
            if ($parentOrSelf->getLocator()->isHtmlRoot())
            {
                continue;
            }

            if (!$this->condition instanceof Exist) // Сначала проверяем что элемент вообще есть в коде страницы
            {
                $existCondition = new Exist();
                $checkResult = $parentOrSelf->accept($existCondition);
                $result->addNext($parentOrSelf, $existCondition, $checkResult);

                if ($checkResult === false) // Дошли до первого несуществующего элемента - дальше идти смысла нет
                {
                    break;
                }
            }

            $checkResult = $parentOrSelf->accept($this->condition); // Затем - само условие
            $result->addNext($parentOrSelf, $this->condition, $checkResult);

//            if ($checkResult === false) // Дошли до первого невалидного элемента - дальше идти смысла нет
//            {
//                break;
//            }
        }

        $result->setProblemNotFound($checkResult !== $this->conditionResult);

        return $result;
    }
}