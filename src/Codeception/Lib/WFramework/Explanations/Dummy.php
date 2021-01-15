<?php


namespace Codeception\Lib\WFramework\Explanations;


use Codeception\Lib\WFramework\Explanations\Result\ExplanationResult;

class Dummy extends AbstractExplanation
{
    public function getName() : string
    {
        return "жалкое оправдание";
    }

    public function acceptWElement($element) : ExplanationResult
    {
        return new ExplanationResult('так получилось');
    }

    public function acceptWBlock($block) : ExplanationResult
    {
        return new ExplanationResult('так получилось');
    }

    public function acceptWCollection($collection) : ExplanationResult
    {
        return new ExplanationResult('так получилось');
    }
}
