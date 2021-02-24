<?php


namespace Codeception\Lib\WFramework\Explanations\Formatters;

use Codeception\Lib\WFramework\Logger\WLogger;

abstract class AbstractExplanationResultVisitor
{
    abstract public function getMessage() : string;

    public function __call($name, $arguments)
    {
        WLogger::logWarning($this, "не содержит метод $name, и поэтому не может распечатать соответствующий результат проверки");
    }
}