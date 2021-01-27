<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Explanations\Result\AbstractExplanationResult;
use Codeception\Lib\WFramework\Explanations\Result\EmptyExplanationResult;

class EmptyExplanation extends AbstractExplanation
{
    public function getName() : string
    {
        return "пустое оправдание";
    }

    public function acceptWElement($element) : AbstractExplanationResult
    {
        return new EmptyExplanationResult();
    }

    public function acceptWBlock($block) : AbstractExplanationResult
    {
        return new EmptyExplanationResult();
    }

    public function acceptWCollection($collection) : AbstractExplanationResult
    {
        return new EmptyExplanationResult();
    }
}
