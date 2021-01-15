<?php


namespace Codeception\Lib\WFramework\Explanations\Formatters;


abstract class AbstractExplanationFormatter
{
    private $message = '';

    abstract protected function format() : string;

    public const EXPLANATIONS_DELIMITER = '=======================================================================================' . PHP_EOL;

    public function getMessage() : string
    {
        if (empty($this->message))
        {
            $this->message .= static::EXPLANATIONS_DELIMITER;
            $this->message .= $this->format();
        }

        return $this->message;
    }
}