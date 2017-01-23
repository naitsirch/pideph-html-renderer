<?php

namespace Pideph\HtmlRenderer\Tests;

use Pideph\HtmlRenderer\LayoutGenerator;

/**
 * Pideph\HtmlRenderer\Tests\RendererTest
 *
 * @author naitsirch
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicLayoutGeneration()
    {
        $generator = LayoutGenerator::fromFile(__DIR__ . '/../resources/templates/basic.html');
        $generator->generate();
    }
}
