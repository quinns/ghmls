<?php
// $Id: data.xml.inc,v 1.2 2009/06/25 14:29:56 jpetso Exp $
/**
 * @file
 * XML Transformations - Transformations for processing XML data.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * A helper class featuring static methods for DOMNode objects.
 */
class TfXMLTools {
  public static function asXMLDocument(DOMNode $node, $formatOutput = TRUE) {
    if ($node->nodeType == XML_DOCUMENT_NODE) {
      $doc = $node;
    }
    else {
      $doc = new DOMDocument();
      $importedNode = $doc->importNode($node, TRUE);
      $doc->appendChild($importedNode);
    }
    $originalFormatOutput = $doc->formatOutput;
    $doc->formatOutput = $formatOutput;
    $xml = $doc->saveXML();
    $doc->formatOutput = $originalFormatOutput;
    return $xml;
  }

  public static function outerXML(DOMNode $node, $formatOutput = TRUE) {
    $doc = ($node->nodeType == XML_DOCUMENT_NODE) ? $node : $node->ownerDocument;

    if (empty($doc)) {
      $doc = new DOMDocument();
    }
    $originalFormatOutput = $doc->formatOutput;
    $doc->formatOutput = $formatOutput;
    $xml = $doc->saveXML(($node->nodeType == XML_DOCUMENT_NODE) ? NULL : $node);
    $doc->formatOutput = $originalFormatOutput;

    return $xml;
  }

  public static function innerXML(DOMNode $node, $formatOutput = TRUE) {
    if ($node->nodeType == XML_TEXT_NODE) { // "Inner XML?" Child nodes? Pah.
      return $node->textContent;
    }

    static $processedChildNodeTypes = array(XML_ELEMENT_NODE, XML_TEXT_NODE);
    $innerXML = '';
    $doc = ($node->nodeType == XML_DOCUMENT_NODE)
      ? $node
      : $node->ownerDocument;

    $originalFormatOutput = $doc->formatOutput;
    $doc->formatOutput = $formatOutput;

    // Loop through all the children, getting the textual representation of
    // each of them.
    $children = $node->childNodes;
    foreach ($children as $child) {
      if (!in_array($child->nodeType, $processedChildNodeTypes)) {
        continue;
      }
      $innerXML .= $doc->saveXML($child);
    }
    $doc->formatOutput = $originalFormatOutput;

    return $innerXML;
  }

  public static function qualifiedName(DOMNode $node) {
    return empty($node->namespaceURI)
      ? $node->localName
      : $node->namespaceURI . ':' . $node->localName;
  }
}

class TfXMLNodeData extends TfConcreteData {
  protected function initialize($data, $importing = FALSE) {
    // We accept plain XML string data for straightforward unserialization.
    if (is_string($data)) {
      $domDocument = new DOMDocument();
      if ($domDocument->loadXML($data, LIBXML_NOCDATA | LIBXML_NOENT | LIBXML_NOBLANKS)) {
        $data = $domDocument;
      }
    }

    // SimpleXML is just not flexible enough to enable iterations like with DOM.
    // Luckily, there's an easy method to convert between those two, so it's
    // possible to have a simple wrapper that takes and delivers SimpleXML data.
    if (is_object($data) && $data instanceof SimpleXMLElement) {
      $domData = dom_import_simplexml($data);
      if ($domData !== FALSE) {
        $data = $domData;
      }
      // otherwise, we keep the original data for the upcoming error message.
    }

    if (!is_object($data) || !($data instanceof DOMNode)) {
      throw new Exception('Tried to initialize a TfXMLNodeData object with incompatible data: "' . print_r($data, TRUE) . '" (' . gettype($data) . ')');
    }
    if ($data->nodeType == XML_DOCUMENT_NODE && is_null($data->documentElement)) {
      throw new Exception('An XML document without document element is invalid and thus won\'t be accepted.');
    }
    parent::initialize($data, $importing);
  }

  public function data() {
    return parent::data();
  }

  protected function serializableData() {
    return TfXMLTools::asXMLDocument($this->data);
  }

  public function isIteratable() {
    return in_array($this->data->nodeType, array(XML_DOCUMENT_NODE, XML_ELEMENT_NODE));
  }

  protected function rawChildElementIterator() {
    $iterator = new TfDOMElementIterator(new TfDOMNodeListIterator($this->data->childNodes));
    return $iterator;
  }
}

/**
 * There's no pre-made iterator for DOMNodeList, so let's take the one that has
 * been kindly posted to http://php.net/manual/en/domnodelist.item.php
 * by "vinyanov at poczta dot onet dot pl".
 */
class TfDOMNodeListIterator implements RecursiveIterator {
  private $nodeList;
  private $offset;

  public function __construct(DOMNodeList $nodeList) {
    $this->nodeList = $nodeList;
  }

  public function rewind() {
    $this->offset = 0;
  }

  public function next() {
    $this->offset++;
  }

  public function valid() {
    return ($this->offset < $this->nodeList->length);
  }

  public function current() {
    return $this->nodeList->item($this->offset);
  }

  public function key() {
    return $this->current()->nodeName;
  }

  public function hasChildren() {
    return isset($this->current()->childNodes->length) && $this->current()->childNodes->length > 0;
  }

  public function getChildren() {
    return new self($this->current()->childNodes);
  }
}

/**
 * Iterator that provides the name of the element as key.
 *
 * Takes a TfDOMNodeListIterator as constructor argument, preferably
 * initialized with $domNode->childNodes.
 */
class TfDOMElementIterator extends FilterIterator {
  public function accept() {
    $element = parent::current();
    switch ($element->nodeType) {
      case XML_ELEMENT_NODE:
        return TRUE;
      case XML_TEXT_NODE:
        $text = trim($element->textContent);
        return !empty($text);
      default:
        return FALSE;
    }
  }

  public function key() {
    $element = parent::current();
    if ($element->nodeType == XML_TEXT_NODE) {
      return '#text';
    }
    return TfXMLTools::qualifiedName($element);
  }
}
