<?php


namespace Codeception\Lib\WFramework\Explanations\Formatter;


class Why
{
    /** @var string */
    protected $message = '';

    /** @var string */
    protected $screenshot = '';

    public function __construct(string $message, string $screenshotBlob = '')
    {
        $this->message = $message;
        $this->screenshot = $screenshotBlob;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function getScreenshot() : string
    {
        return $this->screenshot;
    }

    public function __toString() : string
    {
        return $this->message;
    }
}