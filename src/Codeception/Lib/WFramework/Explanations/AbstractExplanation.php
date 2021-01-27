<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;

/**
 * Class AbstractExplanation
 *
 * @method AbstractExplanationResult acceptWElement($element)
 * @method AbstractExplanationResult acceptWBlock($block)
 * @method AbstractExplanationResult acceptWCollection($collection)
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

    //Диагностика всегда возвращает AbstractExplanationResult с описанием проблемы

    public function __call($name, $arguments) : AbstractExplanationResult
    {
        return parent::__call($name, $arguments);
    }
}
