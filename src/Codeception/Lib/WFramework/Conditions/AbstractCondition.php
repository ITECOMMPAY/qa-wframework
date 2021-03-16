<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Conditions\Interfaces\IWrapOtherCondition;
use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\DefaultExplanation;
use Codeception\Lib\WFramework\Explanations\Formatter\AbstractExplanationResultVisitor;
use Codeception\Lib\WFramework\Explanations\Formatter\DefaultExplanationResultFormatter;
use Codeception\Lib\WFramework\Explanations\Formatter\Why;
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

    protected function explainWhy(AbstractCondition $condition, IPageObject $pageObject, bool $actualValue) : array
    {
        $resultsOfExplanatins = [];

        foreach ($condition->getExplanationClasses() as $explanationClass)
        {
            $explanationResult = $pageObject->accept(new $explanationClass($condition, $actualValue));

            if (!$explanationResult instanceof AbstractExplanationResult)
            {
                throw new UsageException($explanationClass . ' -> должен возвращать наследника AbstractExplanationResult в качестве результата проверки');
            }

            $resultsOfExplanatins []= $explanationResult;
        }

        return $resultsOfExplanatins;
    }

    /**
     * Почему условие выполнилось/не выполнилось для заданного PageObject'а
     *
     * Если нужно сделать более сложную проверку - вместо переопределения этого метода
     * лучше посмотреть в строну переопределения метода explainWhy()
     *
     * @param IPageObject $pageObject   - заданный PageObject
     * @param bool $actualValue         - актуальное значение условия
     * @return string                   - описание проблем
     */
    public function why(IPageObject $pageObject, bool $actualValue = false) : Why
    {
        $condition = $this;

        if ($this instanceof IWrapOtherCondition)
        {
            $condition = $this->getWrappedCondition();
        }

        $resultAggregate = new ExplanationResultAggregate($pageObject, $condition, $actualValue);

        $resultsOfExplanations = $this->withDisabledAutoScroll(
            function () use ($condition, $pageObject, $actualValue) {
                return $this->explainWhy($condition, $pageObject, $actualValue);
            }
        );

        $resultAggregate->addChildren(...$resultsOfExplanations);

        $formatter = $this->getFormatterFromSettings();

        $resultAggregate->accept($formatter);

        return $formatter->getResult();
    }

    /**
     * Отключаем авто-скроллинг, чтобы зафиксировать состояние экрана во время ошибки
     *
     * @param callable $func
     * @return mixed
     */
    private function withDisabledAutoScroll(callable $func)
    {
        $forceScrollToOff = TestProperties::getValue('forceScrollToOff', false);
        TestProperties::setValue('forceScrollToOff', true);

        $result = $func();

        TestProperties::setValue('forceScrollToOff', $forceScrollToOff);

        return $result;
    }

    private function getFormatterFromSettings() : AbstractExplanationResultVisitor
    {
        $formatterClass = TestProperties::getValue('explanationResultFormatter', DefaultExplanationResultFormatter::class);
        $formatter = new $formatterClass;

        if (!$formatter instanceof AbstractExplanationResultVisitor)
        {
            throw new UsageException("$formatterClass должен быть наследником AbstractExplanationResultVisitor");
        }

        return $formatter;
    }

    public function __call($name, $arguments) : bool
    {
        return parent::__call($name, $arguments);
    }
}
