<?php
// $Id: operations.xml.extract.inc,v 1.4 2009/06/27 13:59:58 jpetso Exp $
/**
 * @file
 * XML Transformations - Transformations for processing XML data.
 *
 * This file contains operations for navigating in XML element objects.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLTagInformation() {
  return array(
    'category' => t('XML'),
    'label' => t('Extract tag name information from XML element'),
    'description' => t("Retrieve an XML element's name, namespace and prefix."),
  );
}

class TfXMLTagInformation extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlElement');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlElement') {
      switch ($propertyKey) {
        case 'label':
          return t('XML element');

        case 'expectedType':
          return 'php:class:DOMElement';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('localName', 'namespace', 'prefix', 'qualifiedName', 'prefixedName', 'attributes');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'localName') {
      switch ($propertyKey) {
        case 'label':
          return t('Local name');

        case 'description':
          return t('The plain tag name by itself, without namespace or prefix.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'namespace') {
      switch ($propertyKey) {
        case 'label':
          return t('Namespace');

        case 'description':
          return t('The XML namespace for the tag name (e.g. "http://www.w3.org/1999/xhtml" or "http://www.w3.org/1999/XSL/Transform"), or an empty string if this XML element is not assigned to a namespace.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'prefix') {
      switch ($propertyKey) {
        case 'label':
          return t('Namespace');

        case 'description':
          return t('The prefix for the tag\'s namespace, if any is registered with the XML document (e.g. "html" or "xsl"), or an empty string if this XML element is not assigned to a namespace.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'qualifiedName') {
      switch ($propertyKey) {
        case 'label':
          return t('Qualified name');

        case 'description':
          return t('"<namespace>:<local-name>" if this XML element is assigned to a namespace, or the plain local name if it\'s not.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'qualifiedName') {
      switch ($propertyKey) {
        case 'label':
          return t('Prefixed name');

        case 'description':
          return t('"<prefix>:<local-name>" if this XML element is assigned to a namespace and associated prefix, or the plain local name if it\'s not.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'attributes') {
      switch ($propertyKey) {
        case 'label':
          return t('List of attributes');

        case 'description':
          return t('A string list with all of the element\'s attributes (key: attribute name, value: attribute value).');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $element = $this->input('xmlElement')->data();

    if ($element->nodeType != XML_ELEMENT_NODE) {
      $output->setErrorMessage(t('XML element is not an element node, tag name information cannot be extracted.'));
    }
    $output->set('localName', $element->localName);
    $output->set('namespace', empty($element->namespaceURI) ? '' : $element->namespaceURI);
    $output->set('prefix', empty($element->prefix) ? '' : $element->prefix);
    $output->set('qualifiedName', TfXMLTools::qualifiedName($element));
    $output->set('prefixedName', empty($element->prefix)
      ? $element->localName
      : ($element->prefix . ':' . $element->localName)
    );

    $attributes = array();
    if (empty($this->data->attributes)) {
      foreach ($this->data->attributes as $attribute) {
        $attributes[self::qualifiedName($attribute)] = $attribute->value;
      }
    }
    $output->set('attributes', $attributes);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLXPathQuery() {
  return array(
    'category' => t('XML'),
    'label' => t('XPath query: Navigate to other XML elements'),
    'description' => t('Retrieve a subset of elements, starting from another XML element.'),
  );
}

class TfXMLXPathQuery extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode', 'xpath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML element');

        case 'description':
          return t('The XML element that will be used as context for the XPath query input.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
    elseif ($inputKey == 'xpath') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath query');

        case 'description':
          return t('The XPath expression which will be evaluated relative to the given XML element.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('xmlNodeList', 'length');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'xmlNodeList') {
      switch ($propertyKey) {
        case 'label':
          return t('Extracted XML elements');

        case 'expectedType':
          return 'transformations:list<php:class:DOMNode>';
      }
    }
    elseif ($outputKey == 'length') {
      switch ($propertyKey) {
        case 'label':
          return t('Number of elements');

        case 'description':
          return t('An integer value specifying the number of nodes that resulted from the XPath query.');

        case 'expectedType':
          return 'php:type:number:integer';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $contextNode = $this->input('xmlNode')->data();
    $query = $this->input('xpath')->data();

    if ($contextNode->nodeType == XML_DOCUMENT_NODE) {
      $document = $contextNode;
    }
    else {
      if (strpos($query, '/') === 0 || strpos($query, '..') === 0) {
        $output->setErrorMessage(t('Only XPath queries for descendant elements are allowed.'));
      }
      $document = $contextNode->ownerDocument;
    }

    $xpath = new DOMXPath($document);
    $resultNodes = $xpath->query($query, $contextNode);
    $output->set('xmlNodeList', new TfDOMNodeListIterator($resultNodes));
    $output->set('length', $resultNodes->length);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLXPathEvaluate() {
  return array(
    'category' => t('XML'),
    'label' => t('XPath evaluation: Call an XPath function'),
    'description' => t('Retrieve the result of an XPath function applied to an XML element (e.g. \'./bookstore/book[price > 35]/title[@lang="en"]/text()\').'),
  );
}

class TfXMLXPathEvaluate extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode', 'xpath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML element');

        case 'description':
          return t('The XML element that will be used as context for the XPath expression input.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
    elseif ($inputKey == 'xpath') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath expression');

        case 'description':
          return t('The XPath expression which will be evaluated relative to the given XML element.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('result');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'result') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath function result');

        case 'expectedType':
          return 'php:type:index-type';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $contextNode = $this->input('xmlNode')->data();
    $query = $this->input('xpath')->data();

    $document = ($contextNode->nodeType == XML_DOCUMENT_NODE)
      ? $contextNode
      : $contextNode->ownerDocument;

    $xpath = new DOMXPath($document);
    $result = $xpath->evaluate($query, $contextNode);

    if (!is_numeric($result) && !is_string($result)) {
      $output->setErrorMessage('Function result is not an atomic value (string or number) and therefore not compatible with this operation\'s output data type. For XML element results, use the XPath query operation (TfXMLXPathQuery class).');
      return;
    }
    $output->set('result', $result);
  }
}
