<?php


namespace Codeception\Lib\WFramework\Explanations\Formatter;

use Codeception\Lib\WFramework\Logger\WLogger;

abstract class AbstractExplanationResultVisitor
{
    abstract public function getResult() : Why;

    public function __call($name, $arguments)
    {
        WLogger::logWarning($this, "не содержит метод $name, и поэтому не может распечатать соответствующий результат проверки");
    }
}