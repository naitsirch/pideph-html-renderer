<?php

namespace Pideph\HtmlRenderer\Layout\Handler;

use Pideph\HtmlRenderer\Layout\Element;
use Pideph\HtmlRenderer\Css\Values\BorderStyle;
use Pideph\HtmlRenderer\Css\Values\Color;
use Pideph\HtmlRenderer\Css\Values\Length;

/**
 * Description of BaseHandler
 *
 * @author naitsirch
 */
class BaseHandler implements PreLayoutHandlerInterface, PostLayoutHandlerInterface
{
    public function getPostLayoutPriority()
    {
        return 0;
    }

    public function getPreLayoutPriority()
    {
        return 0;
    }

    public function handlePreLayout(Element $element)
    {
        $this->assignBorderDefinitions($element);
        $this->assignMarginDefinitions($element);
        $this->assignPaddingDefinitions($element);
        $this->assignTextDefinitions($element);
    }

    public function handlePostLayout(Element $element)
    {
        return;
    }

    /**
     * Takes a shorthand value for 1..4 sides and prepares them as array.
     * Example: "1px 2px 3px 4px" => [1px, 2px, 3px, 4px]
     *
     * @param string $shorthandValue
     * @return array
     */
    public static function explodeFourSidesShorthandValue($shorthandValue)
    {
        $values = explode(' ', trim($shorthandValue));

        if (count($values) === 1) {
            $values[1] = $values[2] = $values[3] = $values[0];
        } else if (count($values) === 2) {
            $values[2] = $values[3] = $values[1];
            $values[1] = $values[0];
        }

        return $values;
    }

    /**
     * Documentation:
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border-width
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border-style
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border-color
     *
     * @param Element $element
     */
    private function assignBorderDefinitions(Element $element)
    {
        /**
         * border shorthand property
         */
        if (isset($element->styleRules['border'])) {
            $color = null;
            $width = null;
            $style = null;
            foreach (explode(' ', trim($element->styleRules['border'])) as $value) {
                if (in_array($value, ['thin', 'medium', 'thick'], true)) {
                    // @TODO: This should be made context dependent from layout (unit and resolution)
                    switch ($value) {
                        case 'thin':   $width = new Length(1, 'px'); break;
                        case 'medium': $width = new Length(3, 'px'); break;
                        case 'thick':  $width = new Length(5, 'px'); break;
                    }
                } else if ($isColor = Color::parseIfValid($value)) {
                    $color = $isColor;
                } else if ($length = Length::parseIfValid($value)) {
                    $width = $length;
                } else if (BorderStyle::isValid($value)) {
                    $style = $value;
                }
            }

            if ($color) {
                $element->computedValues['border-top-color']    =
                $element->computedValues['border-right-color']  =
                $element->computedValues['border-bottom-color'] =
                $element->computedValues['border-left-color']   = $color;
            }

            if ($width) {
                $element->computedValues['border-top-width']    =
                $element->computedValues['border-right-width']  =
                $element->computedValues['border-bottom-width'] =
                $element->computedValues['border-left-width']   = $width;
            }

            if ($style) {
                $element->computedValues['border-top-style']    =
                $element->computedValues['border-right-style']  =
                $element->computedValues['border-bottom-style'] =
                $element->computedValues['border-left-style']   = $style;
            }
        }

        /**
         * border color
         */

        if (isset($element->styleRules['border-color'])) {
            $colors = self::explodeFourSidesShorthandValue($element->styleRules['border-color']);

            $element->computedValues['border-top-color']    = new Color($colors[0]);
            $element->computedValues['border-right-color']  = new Color($colors[1]);
            $element->computedValues['border-bottom-color'] = new Color($colors[2]);

            if (isset($colors[3])) {
                $element->computedValues['border-left-color'] = new Color($colors[3]);
            }
        }

        if ($element->styleRules['border-top-color']) {
            $element->computedValues['border-top-color'] = new Color($element->styleRules['border-top-color']);
        }
        if ($element->styleRules['border-right-color']) {
            $element->computedValues['border-right-color'] = new Color($element->styleRules['border-right-color']);
        }
        if ($element->styleRules['border-bottom-color']) {
            $element->computedValues['border-bottom-color'] = new Color($element->styleRules['border-bottom-color']);
        }
        if ($element->styleRules['border-left-color']) {
            $element->computedValues['border-left-color'] = new Color($element->styleRules['border-left-color']);
        }

        /**
         * border width
         */

        if (isset($element->styleRules['border-width'])) {
            $widths = self::explodeFourSidesShorthandValue($element->styleRules['border-width']);

            $element->computedValues['border-top-width']    = new Length($widths[0]);
            $element->computedValues['border-right-width']  = new Length($widths[1]);
            $element->computedValues['border-bottom-width'] = new Length($widths[2]);

            if (isset($widths[3])) {
                $element->computedValues['border-left-width'] = new Length($widths[3]);
            }
        }

        if ($element->styleRules['border-top-width']) {
            $element->computedValues['border-top-width'] = new Length($element->styleRules['border-top-width']);
        }
        if ($element->styleRules['border-right-width']) {
            $element->computedValues['border-right-width'] = new Length($element->styleRules['border-right-width']);
        }
        if ($element->styleRules['border-bottom-width']) {
            $element->computedValues['border-bottom-width'] = new Length($element->styleRules['border-bottom-width']);
        }
        if ($element->styleRules['border-left-width']) {
            $element->computedValues['border-left-width'] = new Length($element->styleRules['border-left-width']);
        }

        /**
         * border style
         */

        if (isset($element->styleRules['border-style'])) {
            $styles = self::explodeFourSidesShorthandValue($element->styleRules['border-style']);

            $element->computedValues['border-top-style']    = $styles[0];
            $element->computedValues['border-right-style']  = $styles[1];
            $element->computedValues['border-bottom-style'] = $styles[2];

            if (isset($styles[3])) {
                $element->computedValues['border-left-style'] = $styles[3];
            }
        }

        if ($element->styleRules['border-top-style']) {
            $element->computedValues['border-top-style'] = $element->styleRules['border-top-style'];
        }
        if ($element->styleRules['border-right-style']) {
            $element->computedValues['border-right-style'] = $element->styleRules['border-right-style'];
        }
        if ($element->styleRules['border-bottom-style']) {
            $element->computedValues['border-bottom-style'] = $element->styleRules['border-bottom-style'];
        }
        if ($element->styleRules['border-left-style']) {
            $element->computedValues['border-left-style'] = $element->styleRules['border-left-style'];
        }

        /**
         * border radius
         */

        if (isset($element->styleRules['border-radius'])) {
            $radii = self::explodeFourSidesShorthandValue($element->styleRules['border-radius']);

            $element->computedValues['border-top-left-radius']    = $radii[0];
            $element->computedValues['border-top-right-radius']  = $radii[1];
            $element->computedValues['border-bottom-right-radius'] = $radii[2];

            if (isset($radii[3])) {
                $element->computedValues['border-bottom-left-radius'] = $radii[3];
            }
        }

        if ($element->styleRules['border-top-left-radius']) {
            $element->computedValues['border-top-left-radius'] = $element->styleRules['border-top-left-radius'];
        }
        if ($element->styleRules['border-top-right-radius']) {
            $element->computedValues['border-top-right-radius'] = $element->styleRules['border-top-right-radius'];
        }
        if ($element->styleRules['border-bottom-right-radius']) {
            $element->computedValues['border-bottom-right-radius'] = $element->styleRules['border-bottom-right-radius'];
        }
        if ($element->styleRules['border-bottom-left-radius']) {
            $element->computedValues['border-bottom-left-radius'] = $element->styleRules['border-bottom-left-radius'];
        }
    }

    /**
     * @param Element $element
     */
    private function assignMarginDefinitions(Element $element)
    {
        if (isset($element->styleRules['margin'])) {
            $margins = self::explodeFourSidesShorthandValue($element->styleRules['margin']);

            $element->computedValues['margin-top']    = new Length($margins[0]);
            $element->computedValues['margin-right']  = new Length($margins[1]);
            $element->computedValues['margin-bottom'] = new Length($margins[2]);

            if (isset($margins[3])) {
                $element->computedValues['margin-left'] = new Length($margins[3]);
            }
        }

        if ($element->styleRules['margin-top']) {
            $element->computedValues['margin-top'] = new Length($element->styleRules['margin-top']);
        }
        if ($element->styleRules['margin-right']) {
            $element->computedValues['margin-right'] = new Length($element->styleRules['margin-right']);
        }
        if ($element->styleRules['margin-bottom']) {
            $element->computedValues['margin-bottom'] = new Length($element->styleRules['margin-bottom']);
        }
        if ($element->styleRules['margin-left']) {
            $element->computedValues['margin-left'] = new Length($element->styleRules['margin-left']);
        }
    }

    /**
     * @param Element $element
     */
    private function assignPaddingDefinitions(Element $element)
    {
        if (isset($element->styleRules['padding'])) {
            $paddings = self::explodeFourSidesShorthandValue($element->styleRules['padding']);

            $element->computedValues['padding-top']    = new Length($paddings[0]);
            $element->computedValues['padding-right']  = new Length($paddings[1]);
            $element->computedValues['padding-bottom'] = new Length($paddings[2]);

            if (isset($paddings[3])) {
                $element->computedValues['padding-left'] = new Length($paddings[3]);
            }
        }

        if ($element->styleRules['padding-top']) {
            $element->computedValues['padding-top'] = new Length($element->styleRules['padding-top']);
        }
        if ($element->styleRules['padding-right']) {
            $element->computedValues['padding-right'] = new Length($element->styleRules['padding-right']);
        }
        if ($element->styleRules['padding-bottom']) {
            $element->computedValues['padding-bottom'] = new Length($element->styleRules['padding-bottom']);
        }
        if ($element->styleRules['padding-left']) {
            $element->computedValues['padding-left'] = new Length($element->styleRules['padding-left']);
        }
    }

    private function assignTextDefinitions(Element $element)
    {
    }
}
