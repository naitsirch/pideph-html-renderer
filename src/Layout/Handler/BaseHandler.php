<?php

namespace Pideph\HtmlRenderer\Layout\Handler;

use Pideph\HtmlRenderer\Layout\Element;
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
     * Documentation:
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border
     * https://developer.mozilla.org/en-US/docs/Web/CSS/border-width
     *
     * @param Element $element
     */
    private function assignBorderDefinition(Element $element)
    {
        if (isset($element->styleRules['border'])) {
            $width = null;
            foreach (explode(' ', trim($element->styleRules['border'])) as $value) {
                if (in_array($value, array('thin', 'medium', 'thick'))) {
                    // @TODO: This should be made context dependent from layout (unit and resolution)
                    switch ($value) {
                        case 'thin':   $width = new Length(1, 'px'); break;
                        case 'medium': $width = new Length(3, 'px'); break;
                        case 'thick':  $width = new Length(5, 'px'); break;
                    }
                } else if ($length = Length::parseIfValid($value)) {
                    $width = $length;
                }
            }

            if ($width) {
                $element->computedValues['border-top-width']    =
                $element->computedValues['border-right-width']  =
                $element->computedValues['border-bottom-width'] =
                $element->computedValues['border-left-width']   = $width;
            }
        }

        if (isset($element->styleRules['border-width'])) {
            $widths = explode(' ', trim($element->styleRules['border-width']));

            if (count($widths) === 1) {
                $widths[1] = $widths[2] = $widths[3] = $widths[0];
            } else if (count($widths) === 2) {
                $widths[2] = $widths[3] = $widths[1];
                $widths[1] = $widths[0];
            }

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
    }
}
