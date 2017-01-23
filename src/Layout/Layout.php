<?php

namespace Pideph\HtmlRenderer\Layout;

use DOMDocument;
use DOMElement;
use SplObjectStorage;
use Sabberworm\CSS\CSSList\Document as CssDocument;

/**
 * Pideph\HtmlRenderer\Layout\Layout
 *
 * @author naitsirch
 */
class Layout
{
    /**
     * @var DOMDocument
     */
    private $document;

    /**
     * @var array
     */
    private $options;

    /**
     * @var CssDocument[]
     */
    private $cssResources = array();

    /**
     * @var SplObjectStorage
     */
    private $elements;

    /**
     * @var array
     */
    private $errors = array();

    public function __construct(DOMDocument $document, array $options = array())
    {
        $this->document = $document;
        $this->options = array_replace_recursive($this->getDefaultOptions(), $options);
        $this->elements = new SplObjectStorage();

        $this->initLayoutElements($document->documentElement);
    }

    /**
     * @return CssDocument[]
     */
    public function getCssResources()
    {
        return $this->cssResources;
    }

    public function addCssResource(CssDocument $cssResource, $source = null)
    {
        if ($source) {
            $this->cssResources[$source] = $cssResource;
        } else {
            $this->cssResources[] = $cssResource;
        }
        return $this;
    }

    /**
     * @return Element
     */
    public function getElement(DOMElement $domElement)
    {
        return $this->elements[$domElement];
    }

    /**
     * @return SplObjectStorage
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function setElements(SplObjectStorage $elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($type, $message)
    {
        $this->errors[$type][] = $message;
    }
    
    private function getDefaultOptions()
    {
        return array(
            'viewport' => array(
                'width'  => 1024,
                'height' => 768,
            ),
            'unit' => 'px',
            'resolution' => 102, // ppi
        );
    }

    private function initLayoutElements(DOMElement $domElement)
    {
        $element = new Element($domElement, $this);

        $this->elements[$domElement] = $element;

        foreach ($domElement->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $this->initLayoutElements($child);
            }
        }
    }
}