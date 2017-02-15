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

    public function __construct($length)
    {
        $parsed = self::parse($length);

        $this->value = $parsed[0];
        $this->unit = $parsed[1];
    }

    public function __toString()
    {
        return $this->value . $this->unit;
    }

    /**
     * Parses the given string and returns an instance of Length if it is valid - otherwise FALSE.
     *
     * @param string $string
     * @return Length|boolean
     */
    public static function parseIfValid($string)
    {
        try {
            return new self($string);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Check if the given string is a valid length value.
     *
     * @param string $string
     * @return boolean
     */
    public static function isValid($string)
    {
        try {
            self::parse($string);
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
    private static function parse($string)
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
