<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Explanations\Result\ExplanationResult;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;

/**
 * Class AbstractExplanation
 *
 * @method ExplanationResult acceptWElement($element)
 * @method ExplanationResult acceptWBlock($block)
 * @method ExplanationResult acceptWCollection($collection)
 *
 * @package Codeception\Lib\WFramework\Explanations
 */
abstract class AbstractExplanation extends PageObjectVisitor
{
    /**
     * @var AbstractCondition
     */
    protected $condition;

    /**
     * @var bool
     */
    protected $actualValue;

    public function __construct(AbstractCondition $condition, bool $actualValue = true)
    {
        $this->condition = $condition;
        $this->actualValue = $actualValue;
    }

    //Диагностика всегда возвращает ExplanationResult с описанием проблемы

    public function __call($name, $arguments) : ExplanationResult
    {
        return parent::__call($name, $arguments);
    }
}
