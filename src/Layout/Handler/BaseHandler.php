<?php

namespace Pideph\HtmlRenderer\Layout\Handler;

use Pideph\HtmlRenderer\Layout\Element;
use Pideph\HtmlRenderer\Css\Values\BorderStyle;
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
        $this->assignBorderDefinition($element);
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
    private function assignBorderDefinition(Element $element)
    {
        /**
         * border shorthand property
         */
        if (isset($element->styleRules['border'])) {
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
                } else if ($length = Length::parseIfValid($value)) {
                    $width = $length;
                } else if (BorderStyle::isValid($value)) {
                    $style = $value;
                }
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
         * border width
         */

        if (isset($element->styleRules['border-width'])) {
            $widths = self::explodeFourSidesShorthandValue($element->styleRules['border-width']);

            $element->computedValues['border-top-width']    = Length::parse($widths[0]);
            $element->computedValues['border-right-width']  = Length::parse($widths[1]);
            $element->computedValues['border-bottom-width'] = Length::parse($widths[2]);

            if (isset($widths[3])) {
                $element->computedValues['border-left-width'] = Length::parse($widths[3]);
            }
        }

        if ($element->styleRules['border-top-width']) {
            $element->computedValues['border-top-width'] = Length::parse($element->styleRules['border-top-width']);
        }
        if ($element->styleRules['border-right-width']) {
            $element->computedValues['border-right-width'] = Length::parse($element->styleRules['border-right-width']);
        }
        if ($element->styleRules['border-bottom-width']) {
            $element->computedValues['border-bottom-width'] = Length::parse($element->styleRules['border-bottom-width']);
        }
        if ($element->styleRules['border-left-width']) {
            $element->computedValues['border-left-width'] = Length::parse($element->styleRules['border-left-width']);
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
    }
}
