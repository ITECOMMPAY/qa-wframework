<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Helpers\PageObjectVisitor;

/**
 * Class AbstractExplanation
 *
 * @method string acceptWElement($element)
 * @method string acceptWBlock($block)
 * @method string acceptWCollection($collection)
 *
 * @package Codeception\Lib\WFramework\Explanations
 */
abstract class AbstractExplanation extends PageObjectVisitor
{
    /**
     * @var bool
     */
    protected $actualValue;

    public function __construct(bool $actualValue = true)
    {
        $this->actualValue = $actualValue;
    }

    //Диагностика всегда возвращает текст с описанием проблемы

    public function __call($name, $arguments) : string
    {
        return parent::__call($name, $arguments);
    }
}
