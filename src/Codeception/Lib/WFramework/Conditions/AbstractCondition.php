<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Exceptions\UsageException;
use Codeception\Lib\WFramework\Explanations\EmptyExplanation;
use Codeception\Lib\WFramework\Explanations\Formatters\DefaultExplanationResultFormatter;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResultAggregate;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;
use Codeception\Lib\WFramework\Logger\WLogger;
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
        return [EmptyExplanation::class];
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
        $resultAggregate = new ExplanationResultAggregate($pageObject, $this, $actualValue);

        foreach ($this->getExplanationClasses() as $explanationClass)
        {
            $explanationResult = $pageObject->accept(new $explanationClass($this, $actualValue));

            if (!$explanationResult instanceof AbstractExplanationResult)
            {
                throw new UsageException($explanationClass . ' -> должен возвращать наследника AbstractExplanationResult в качестве результата проверки');
            }

            $resultAggregate->addChild($explanationResult);
        }

        $formatter = new DefaultExplanationResultFormatter();

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
