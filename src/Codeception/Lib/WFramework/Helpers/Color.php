<?php


namespace Codeception\Lib\WFramework\Helpers;


use Codeception\Lib\WFramework\Exceptions\Common\UsageException;
use function abs;
use Codeception\Lib\WFramework\Exceptions\Common\NotImplementedException;
use function preg_match;

class Color
{
    /** @var int */
    public $red;

    /** @var int */
    public $green;

    /** @var int */
    public $blue;

    /** @var float */
    public $alpha;

    public function __construct(int $red, int $green, int $blue, float $alpha = 1.0)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    public static function fromHex(string $hex) : Color
    {
        $firstSymbol = $hex[0] ?? '';

        if ($firstSymbol === '#')
        {
            $hex = substr($hex, 1);
        }

        $len = strlen($hex);

        if ($len === 6)
        {
            $values = str_split($hex, 2);
        }
        elseif ($len === 3)
        {
            $values = str_split($hex, 1);
            array_map(function ($v) {return "$v$v";}, $values);
        }
        else
        {
            throw new UsageException('Цвет должен состоять из трёх или шести HEX цифр');
        }

        return new Color(hexdec($values[0]), hexdec($values[1]), hexdec($values[2]), 1.0);
    }

    public static function fromValues(int $red, int $green, int $blue, float $alpha = 1.0) : Color
    {
        return new Color($red, $green, $blue, $alpha);
    }

    public static function fromImagickColor(array $imagickColorArray) : Color
    {
        return new Color($imagickColorArray['r'], $imagickColorArray['g'], $imagickColorArray['b'], $imagickColorArray['a'] ?? 1.0);
    }

    public static function fromString(string $string) : Color
    {
        $regex = '%rgb.*\((\d+),\s*(\d+),\s*(\d+)(,(\s*(\d+)(\.(\d+))))?%m';

        $matches = [];

        preg_match($regex, $string, $matches);

        if (!isset($matches[1], $matches[2], $matches[3]))
        {
            throw new NotImplementedException('Браузер вернул цвет элемента в неподдерживаемом формате: ' . $string);
        }

        $alpha = (float) ($matches[5] ?? 1.0);

        return new Color((int) $matches[1], (int) $matches[2], (int) $matches[3], $alpha);
    }

    public function equals(Color $otherColor) : bool
    {
        return $this->red === $otherColor->red &&
               $this->green === $otherColor->green &&
               $this->blue === $otherColor->blue &&
               abs($this->alpha - $otherColor->alpha) < PHP_FLOAT_EPSILON;
    }

    public function __toString() : string
    {
        if (abs($this->alpha - 1.0) > PHP_FLOAT_EPSILON)
        {
            return "rgba($this->red, $this->green, $this->blue, $this->alpha)";
        }

        return "rgb($this->red, $this->green, $this->blue)";
    }
}
