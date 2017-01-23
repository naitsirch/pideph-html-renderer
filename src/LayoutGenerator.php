<?php

namespace Pideph\HtmlRenderer;

use DOMDocument;
use DOMElement;
use DOMXPath;
use SplObjectStorage;
use Sabberworm\CSS\Parser;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Pideph\HtmlRenderer\Layout\Element;
use Pideph\HtmlRenderer\Layout\Layout;
use Pideph\HtmlRenderer\Layout\Handler\BaseHandler;
use Pideph\HtmlRenderer\Layout\Handler\PreLayoutHandlerInterface;
use Pideph\HtmlRenderer\Layout\Handler\PostLayoutHandlerInterface;

/**
 * Pideph\HtmlRenderer\LayoutGenerator
 *
 * @author naitsirch
 */
class LayoutGenerator
{
    /**
     * @var DOMDocument
     */
    private $document;
    
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var array|PreLayoutHandlerInterface[]
     */
    private $preLayoutHandlers = array();

    /**
     * @var array|PostLayoutHandlerInterface[]
     */
    private $postLayoutHandlers = array();

    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
    }

    public function generate(array $options = array())
    {
        $this->layout = new Layout($this->document, $options);

        $this->loadCss();
        $this->assignCssRulesToElements();

        $baseHandler = new BaseHandler();
        $this->addPreLayoutHandler($baseHandler);
        $this->addPostLayoutHandler($baseHandler);

        $this->visitElements($this->document->documentElement);
    }

    public function addPreLayoutHandler(PreLayoutHandlerInterface $handler)
    {
        $this->preLayoutHandlers[] = $handler;
    }

    public function addPostLayoutHandler(PostLayoutHandlerInterface $handler)
    {
        $this->postLayoutHandlers[] = $handler;
    }

    public static function fromHtmlString($html)
    {
        $document = new \DOMDocument();
        $document->loadHTML($html);
        return new self($document);
    }

    public static function fromFile($fileName)
    {
        $document = new \DOMDocument();
        $document->load($fileName);
        return new self($document);
    }
    
    private function loadCss()
    {
        $sources = array();
        
        // Get all style sources from the file
//        $xpath = new \DOMXPath($this->document);
//        $nodes = $xpath->query('/html/head/style');
//
//        for ($i = $nodes->length; $i >= 0; $i--) {
//            $node = $nodes->item($i); /* @var $node \DOMNode */
//            if ($node->attributes['media']) {
//
//            }
//            //array_unshift($sources, ->
//        }
        
        array_unshift($sources, 'file:///' . realpath(__DIR__ . '/../resources/css/default.css'));
        
        foreach ($sources as $source) {
            $parser = new Parser(file_get_contents($source));
            $result = $parser->parse();

            $this->layout->addCssResource($result, $source);
        }
    }

    private function assignCssRulesToElements()
    {
        $converter = new CssSelectorConverter();
        $xPath = new DOMXPath($this->document);

        $declarationBlockDataArray = new SplObjectStorage();

        foreach ($this->layout->getCssResources() as $source => $resource) {
            foreach ($resource->getAllDeclarationBlocks() as $declarationBlock) {
                /* @var $declarationBlock \Sabberworm\CSS\RuleSet\DeclarationBlock */

                foreach ($declarationBlock->getSelectors() as $selector) {
                    /* @var $selector \Sabberworm\CSS\Property\Selector */
                    try {
                        $xPathSelector = $converter->toXPath($selector->getSelector());
                    } catch (\Exception $e) {
                        $msg = '%s In line %s in file %s';
                        $msg = sprintf($msg, $e->getMessage(), $declarationBlock->getLineNo(), $source);
                        $this->layout->addError('css', $msg);
                    }
                    $nodeList = $xPath->query($xPathSelector);

                    foreach ($nodeList as $node) {
                        $blockDataArray = isset($declarationBlockDataArray[$node]) ? $declarationBlockDataArray[$node] : array();

                        $blockDataArray[] = array(
                            'specificity' => $selector->getSpecificity(),
                            'selector' => $selector,
                            'declarationBlock' => $declarationBlock,
                        );

                        $declarationBlockDataArray[$node] = $blockDataArray;
                    }
                }
            }
        }

        // we have to sort the rules by specificity
        foreach ($declarationBlockDataArray as $node) {
            $element = $this->layout->getElement($node);
            $blockDataArray = $declarationBlockDataArray[$node];

            usort($blockDataArray, function ($a, $b) {
                return $a['specificity'] - $b['specificity'];
            });

            foreach ($blockDataArray as $blockData) {
                foreach ($blockData['declarationBlock']->getRules() as $rule) {
                    /* @var $rule \Sabberworm\CSS\Rule\Rule */
                    $element->styleRules[$rule->getRule()] = array(
                        'value' => $rule->getValue(),
                        'selector' => $blockData['selector']->getSelector(),
                    );
                }
            }
        }
    }

    private function visitElements(DOMElement $domElement = null)
    {
        $element = $this->layout->getElement($domElement);

        foreach ($this->preLayoutHandlers as $handler) {
            $handler->handlePreLayout($element);
        }

        foreach ($domElement->childNodes as $child) {
            if ($child instanceof \DOMText) {
                continue;
            }
            $this->visitElements($child);
        }

        foreach ($this->postLayoutHandlers as $handler) {
            $handler->handlePostLayout($element);
        }
    }
}
