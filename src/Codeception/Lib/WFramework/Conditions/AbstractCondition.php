<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\DefaultExplanation;
use Codeception\Lib\WFramework\Explanations\Formatters\AbstractExplanationResultVisitor;
use Codeception\Lib\WFramework\Explanations\Formatters\DefaultExplanationResultFormatter;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResultAggregate;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;
use Codeception\Lib\WFramework\Properties\TestProperties;
use Codeception\Lib\WFramework\WebObjects\Base\Interfaces\IPageObject;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

/**
 * Class AbstractCondition
 *
 * @method bool acceptWElement(WElement $element)
 * @method bool acceptWBlock(WBlock $block)
 * @method bool acceptWCollection(WCollection $collection)
 *
 * @package Codeception\Lib\WFramework\Conditions
 */
abstract class AbstractCondition extends PageObjectVisitor
{
    /**
     * @return string[] - массив полных названий классов диагностики
     */
    protected function getExplanationClasses() : array
    {
        return [DefaultExplanation::class];
    }

    /**
     * Почему условие выполнилось/не выполнилось для заданного PageObject'а
     *
     * @param IPageObject $pageObject   - заданный PageObject
     * @param bool $actualValue         - актуальное значение условия
     * @return string                   - описание проблем
     */
    public function why(IPageObject $pageObject, bool $actualValue = false) : string
    {
        $condition = $this;

        if ($this instanceof IWrapOtherCondition)
        {
            $condition = $this->getWrappedCondition();
        }

        $resultAggregate = new ExplanationResultAggregate($pageObject, $condition, $actualValue);

        foreach ($condition->getExplanationClasses() as $explanationClass)
        {
            $explanationResult = $pageObject->accept(new $explanationClass($condition, $actualValue));

            if (!$explanationResult instanceof AbstractExplanationResult)
            {
                throw new UsageException($explanationClass . ' -> должен возвращать наследника AbstractExplanationResult в качестве результата проверки');
            }

            $resultAggregate->addChild($explanationResult);
        }

        $formatterClass = TestProperties::getValue('explanationResultFormatter', DefaultExplanationResultFormatter::class);
        $formatter = new $formatterClass;

        if (!$formatter instanceof AbstractExplanationResultVisitor)
        {
            throw new UsageException("$formatterClass должен быть наследником AbstractExplanationResultVisitor");
        }

        /** @var AbstractExplanationResult $explanationResult */
        foreach ($resultAggregate->traverseDepthFirst() as $explanationResult)
        {
            $explanationResult->accept($formatter);
        }

        return $formatter->getMessage();
    }

    public function __call($name, $arguments) : bool
    {
        return parent::__call($name, $arguments);
    }
}
