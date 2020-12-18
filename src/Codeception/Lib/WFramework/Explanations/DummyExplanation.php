<?php


namespace Codeception\Lib\WFramework\Explanations;


class DummyExplanation extends AbstractExplanation
{
    public function acceptWElement($element) : string
    {
        return 'так получилось';
    }

    public function acceptWBlock($block) : string
    {
        return 'так получилось';
    }

    public function acceptWCollection($collection) : string
    {
        return 'так получилось';
    }
}
