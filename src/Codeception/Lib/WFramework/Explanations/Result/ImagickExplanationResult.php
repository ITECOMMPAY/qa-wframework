<?php


namespace Codeception\Lib\WFramework\Explanations\Result;


class ImagickExplanationResult extends TextExplanationResult
{
    /** @var string */
    protected $background;

    /** @var \Imagick */
    protected $explanationLayer;

    public function __construct(string $text, string $background = '', \Imagick $explanationLayer = null)
    {
        parent::__construct($text);

        $this->background       = $background;
        $this->explanationLayer = $explanationLayer;
    }

    public function getBackground() : string
    {
        return $this->background;
    }

    public function getExplanationLayer() : ?\Imagick
    {
        return $this->explanationLayer;
    }
}