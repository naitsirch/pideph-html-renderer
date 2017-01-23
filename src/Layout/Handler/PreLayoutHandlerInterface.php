<?php

namespace Pideph\HtmlRenderer\Layout\Handler;

use Pideph\HtmlRenderer\Layout\Element;

/**
 * Pideph\HtmlRenderer\Layout\Handler\PreLayoutHandlerInterface
 *
 * @author naitsirch
 */
interface PreLayoutHandlerInterface
{
    function handlePreLayout(Element $element);

    function getPreLayoutPriority();
}
