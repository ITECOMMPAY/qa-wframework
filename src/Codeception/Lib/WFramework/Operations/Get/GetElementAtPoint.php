<?php


namespace Codeception\Lib\WFramework\Operations\Get;


use Codeception\Lib\WFramework\Conditions\Exist;
use Codeception\Lib\WFramework\Operations\AbstractOperation;
use Codeception\Lib\WFramework\WebDriverProxies\ProxyWebElement;
use Codeception\Lib\WFramework\WebObjects\Base\WElement\WElement;
use Codeception\Lib\WFramework\WebObjects\Base\WPageObject;
use Codeception\Lib\WFramework\WLocator\WLocator;

class GetElementAtPoint extends AbstractOperation
{
    public function getName() : string
    {
        return "получаем HTML-элемент по координатам X:$this->x; Y:$this->y";
    }

    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * @var string|WElement
     */
    protected $wElementClass;

    /**
     * Получаем элемент по координатам и оборачиваем его в заданный класс
     */
    public function __construct(string $wElementClass, int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->wElementClass = $wElementClass;
    }

    public function acceptWBlock($block) : WElement
    {
        return $this->apply($block);
    }

    public function acceptWElement($element) : WElement
    {
        return $this->apply($element);
    }

    protected function apply(WPageObject $pageObject) : WElement
    {
        $xpath = $pageObject->should(new Exist())->returnSeleniumElement()->executeScript(static::GET_XPATH_AT_COORDINATES, [$this->x, $this->y]);

        $proxyWebElement = new ProxyWebElement(WLocator::xpath($xpath), $pageObject->returnSeleniumServer(), $pageObject->getTimeout());

        return $this->wElementClass::fromProxyWebElement('temporary element', $proxyWebElement, $pageObject);
    }

    protected const GET_XPATH_AT_COORDINATES = <<<EOF
    let node = document.elementFromPoint(arguments[0], arguments[1]);

    return getXPathForNode(node, false);
    
    //https://github.com/chromium/chromium/blob/77578ccb4082ae20a9326d9e673225f1189ebb63/third_party/blink/renderer/devtools/front_end/elements/DOMPath.js
    
    function getXPathForNode(node, optimized) {
      if (node.nodeType === Node.DOCUMENT_NODE)
        return '/';
    
      const steps = [];
      let contextNode = node;
      while (contextNode) {
        const step = getXPathValue(contextNode, optimized);
        if (!step)
          break;  // Error - bail out early.
        steps.push(step);
        if (step.optimized)
          break;
        contextNode = contextNode.parentNode;
      }
    
      steps.reverse();
      return (steps.length && steps[0].optimized ? '' : '/') + steps.join('/');
    };
    
    function getXPathValue(node, optimized) {
      let ownValue;
      
      let Step = class {
        constructor(value, optimized) {
          this.value = value;
          this.optimized = optimized || false;
        }
      
        toString() {
          return this.value;
        }
      };
      
      const ownIndex = getXPathIndex(node);
      if (ownIndex === -1)
        return null;  // Error.
    
      switch (node.nodeType) {
        case Node.ELEMENT_NODE:
          if (optimized && node.getAttribute('id'))
            return new Step('//*[@id="' + node.getAttribute('id') + '"]', true);
          ownValue = node.localName;
          break;
        case Node.ATTRIBUTE_NODE:
          ownValue = '@' + node.nodeName;
          break;
        case Node.TEXT_NODE:
        case Node.CDATA_SECTION_NODE:
          ownValue = 'text()';
          break;
        case Node.PROCESSING_INSTRUCTION_NODE:
          ownValue = 'processing-instruction()';
          break;
        case Node.COMMENT_NODE:
          ownValue = 'comment()';
          break;
        case Node.DOCUMENT_NODE:
          ownValue = '';
          break;
        default:
          ownValue = '';
          break;
      }
    
      if (ownIndex > 0)
        ownValue += '[' + ownIndex + ']';
    
      return new Step(ownValue, node.nodeType === Node.DOCUMENT_NODE);
    };
    
    
    function getXPathIndex(node) {
      // Returns -1 in case of error, 0 if no siblings matching the same expression, <XPath index among the same expression-matching sibling nodes> otherwise.
      function areNodesSimilar(left, right) {
        if (left === right)
          return true;
    
        if (left.nodeType === Node.ELEMENT_NODE && right.nodeType === Node.ELEMENT_NODE)
          return left.localName === right.localName;
    
        if (left.nodeType === right.nodeType)
          return true;
    
        // XPath treats CDATA as text nodes.
        const leftType = left.nodeType === Node.CDATA_SECTION_NODE ? Node.TEXT_NODE : left.nodeType;
        const rightType = right.nodeType === Node.CDATA_SECTION_NODE ? Node.TEXT_NODE : right.nodeType;
        return leftType === rightType;
      }
    
      const siblings = node.parentNode ? node.parentNode.children : null;
      if (!siblings)
        return 0;  // Root node - no siblings.
      let hasSameNamedElements;
      for (let i = 0; i < siblings.length; ++i) {
        if (areNodesSimilar(node, siblings[i]) && siblings[i] !== node) {
          hasSameNamedElements = true;
          break;
        }
      }
      if (!hasSameNamedElements)
        return 0;
      let ownIndex = 1;  // XPath indices start with 1.
      for (let i = 0; i < siblings.length; ++i) {
        if (areNodesSimilar(node, siblings[i])) {
          if (siblings[i] === node)
            return ownIndex;
          ++ownIndex;
        }
      }
      return -1;  // An error occurred: |node| not found in parent's children.
    };
EOF;

}