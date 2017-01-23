<?php

namespace Pideph\HtmlRenderer\Layout\Handler;

use Pideph\HtmlRenderer\Layout\Element;

/**
 * Pideph\HtmlRenderer\Layout\Handler\PostLayoutHandlerInterface
 *
 * @author naitsirch
 */
interface PostLayoutHandlerInterface
{
    function handlePostLayout(Element $element);

    function getPostLayoutPriority();
}
