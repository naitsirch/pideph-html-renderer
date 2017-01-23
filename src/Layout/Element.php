<?php

namespace Pideph\HtmlRenderer\Layout;

use DOMElement;
use Pideph\HtmlRenderer\Css\Values\Length;
use Pideph\HtmlRenderer\Layout\Layout;

/**
 * Pideph\HtmlRenderer\Layout\Element
 *
 * @author naitsirch
 */
class Element
{
    private $domElement;

    private $layout;

    public $computedValues = array(
        'border-top-width' => null, 'border-right-width' => null, 'border-bottom-width' => null, 'border-left-width' => null,
    );

    public $styleRules = array();

    public $clientHeight;
    
    public $clientLeft;

    public $clientTop;

    public $clientWidth;

    public $offsetLeft;

    public $offsetHeight;

    public $offsetTop;

    public $offsetWidth;

    public function __construct(DOMElement $domElement, Layout $layout)
    {
        $this->domElement = $domElement;
        $this->layout = $layout;
    }

    /**
     * @return DOMElement
     */
    public function getDomElement()
    {
        return $this->domElement;
    }
}