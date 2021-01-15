<?php


namespace Codeception\Lib\WFramework\Conditions;


use Codeception\Lib\WFramework\Explanations\Dummy;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResult;
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
        return [Dummy::class];
    }

    /**
     * Почему условие выполнилось/не выполнилось для заданного PageObject'а
     *
     * @param IPageObject $pageObject   - заданный PageObject
     * @param bool $actualValue         - актуальное значение условия
     * @return ExplanationResult[]      - список причин
     */
    public function why(IPageObject $pageObject, bool $actualValue = false) : array
    {
        $result = [];

        foreach ($this->getExplanationClasses() as $explanationClass)
        {
            $result[] = $pageObject->accept(new $explanationClass($this, $actualValue));
        }

        return $result;
    }

    public function __call($name, $arguments) : bool
    {
        return parent::__call($name, $arguments);
    }
}
