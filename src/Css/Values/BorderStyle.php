<?php

namespace Pideph\HtmlRenderer\Css\Values;

/**
 * Description of BorderStyle
 *
 * @author naitsirch
 */
class BorderStyle
{
    private static $styles = [
        'none',
        'hidden',
        'dotted',
        'dashed',
        'solid',
        'double',
        'groove',
        'ridge',
        'inset',
        'outset',
    ];

    public static function getStyles()
    {
        return self::$styles;
    }

    public static function isValid($style)
    {
        return in_array($style, self::$styles, true);
    }
}
