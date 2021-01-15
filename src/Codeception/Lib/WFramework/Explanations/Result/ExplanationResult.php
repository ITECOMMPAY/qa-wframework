<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


class ExplanationResult
{
    protected $message = '';

    protected $context = [];

    public function __construct(string $message, array $context = [])
    {
        $this->message = $message;

        $this->context = $context;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function getContext() : array
    {
        return $this->context;
    }

    public function __toString() : string
    {
        return $this->getMessage();
    }
}