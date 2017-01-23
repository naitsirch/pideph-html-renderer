<?php

namespace Pideph\HtmlRenderer\Css\Values;

/**
 * Pideph\HtmlRenderer\Css\Values\Length
 *
 * Documentation: https://developer.mozilla.org/en-US/docs/Web/CSS/length#Interpolation
 *
 * @author naitsirch
 */
class Length
{
    public $value;

    public $unit;

    public function __construct($value, $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    /**
     * Parse a length value.
     *
     * @param string $string
     * @return Length
     * @throws \InvalidArgumentException If $string is not a valid length.
     */
    public static function parse($string)
    {
        $parsed = self::_parse($string);
        return new self($parsed[0], $parsed[1]);
    }

    public static function parseIfValid($string)
    {
        try {
            return self::parse($string);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    public static function isValid($string)
    {
        try {
            self::_parse($string);
            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string $string
     * @return array
     * @throws \InvalidArgumentException If $string is not a valid length.
     */
    private static function _parse($string)
    {
        $value = '';
        $unit = '';
        for ($i = strlen($string) - 1; $i >= 0; $i--) {
            $dec = ord($string[$i]);
            if (($dec >= 48 && $dec <= 57) || '.' === $string[$i] || '-' === $string[$i]) {
                $value = $string[$i] . $value;
            } else {
                $unit = $string[$i] . $unit;
            }
        }

        if (!is_numeric($value) || !in_array($unit, array('%', 'em', 'ex', 'ch', 'rem', 'vh', 'vw', 'vmin', 'vmax', 'px', 'mm', 'q', 'cm', 'in', 'pt', 'pc'))) {
            throw new \InvalidArgumentException($string . ' is not a valid length.');
        }

        return array($value, $unit);
    }
}
