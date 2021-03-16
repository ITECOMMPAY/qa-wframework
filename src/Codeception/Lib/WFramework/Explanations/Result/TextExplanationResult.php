<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


class TextExplanationResult extends AbstractExplanationResult
{
    /**
     * @var string
     */
    protected $text;

    public function __construct(string $text)
    {
        parent::__construct();

        $this->text = $text;
    }

    public function getText() : string
    {
        return $this->text;
    }
}