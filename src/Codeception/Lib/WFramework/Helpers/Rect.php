<?php


namespace Codeception\Lib\WFramework\Helpers;


class Rect
{
    public $x;

    public $y;

    public $width;

    public $height;

    public $top;

    public $bottom;

    public $left;

    public $right;

    public function __construct(float $x, float $y, float $width, float $height, float $top, float $bottom, float $left, float $right)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        $this->top = $top;
        $this->bottom = $bottom;
        $this->left = $left;
        $this->right = $right;
    }

    public static function fromDOMRect(array $domRect) : Rect
    {
        return new Rect($domRect['x'], $domRect['y'], $domRect['width'], $domRect['height'], $domRect['top'], $domRect['bottom'], $domRect['left'], $domRect['right']);
    }

    public static function fromOtherRect(Rect $otherRect, array $modifiers = []) : Rect
    {
        $result = new Rect($otherRect->x, $otherRect->y, $otherRect->width, $otherRect->height, $otherRect->top, $otherRect->bottom, $otherRect->left, $otherRect->right);

        foreach ($modifiers as $field => $value)
        {
            $result->$field = $value;
        }

        return $result;
    }

    public function __toString() : string
    {
        return "X: $this->x Y: $this->y    Size: {$this->width}x{$this->height}    T:$this->top B: $this->bottom L: $this->left R: $this->right";
    }

    /**
     * @return float
     */
    public function getX() : float
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY() : float
    {
        return $this->y;
    }

    /**
     * @return float
     */
    public function getWidth() : float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getHeight() : float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getTop() : float
    {
        return $this->top;
    }

    /**
     * @return float
     */
    public function getBottom() : float
    {
        return $this->bottom;
    }

    /**
     * @return float
     */
    public function getLeft() : float
    {
        return $this->left;
    }

    /**
     * @return float
     */
    public function getRight() : float
    {
        return $this->right;
    }
}
