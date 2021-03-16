<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Conditions\AbstractCondition;
use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;
use Codeception\Lib\WFramework\WebObjects\Base\WBlock\WBlock;
use Codeception\Lib\WFramework\WebObjects\Base\WCollection\WCollection;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;

/**
 * Class AbstractExplanation
 *
 * @method AbstractExplanationResult acceptWElement(WElement $element)
 * @method AbstractExplanationResult acceptWBlock(WBlock $block)
 * @method AbstractExplanationResult acceptWCollection(WCollection $collection)
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
    protected $conditionResult;

    public function getName() : string
    {
        return "почему " . ($this->conditionResult ? '' : 'НЕ ') . $this->condition;
    }

    public function __construct(AbstractCondition $condition, bool $conditionResult = true)
    {
        $this->condition = $condition;
        $this->conditionResult = $conditionResult;
    }

    //Диагностика всегда возвращает AbstractExplanationResult с описанием проблемы

    public function __call($name, $arguments) : AbstractExplanationResult
    {
        return parent::__call($name, $arguments);
    }
}
