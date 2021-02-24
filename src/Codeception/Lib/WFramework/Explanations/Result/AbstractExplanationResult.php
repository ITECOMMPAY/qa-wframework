<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


use Codeception\Lib\WFramework\Helpers\Composite;

abstract class AbstractExplanationResult extends Composite
{
    public function getName() : string
    {
        return $this->hash(); // Результатам не нужны человеко-читаемые имена
    }
}