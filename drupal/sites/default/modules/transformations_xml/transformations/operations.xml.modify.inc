<?php
// $Id: operations.xml.modify.inc,v 1.4 2009/06/27 15:05:44 jpetso Exp $
/**
 * @file
 * XML Transformations - Transformations for processing XML data.
 *
 * This file contains operations for navigating in XML element objects.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * A helper class featuring static methods for modifying DOMNode objects.
 */
class TfXMLModificationTools {
  /**
   * Import the DOM @p $node into the DOM @p $document if appropriate.
   * The @p $node is left unchanged if it's a document node itself, or if its
   * owner document is already the given @p $document.
   *
   * @return
   *   The imported node (or the original one if no import was necessary).
   */
  public static function importNode(&$document, $node) {
    if ($node->nodeType == XML_DOCUMENT_NODE) {
      return $node;
    }
    if ($node->ownerDocument === $document) {
      return $node;
    }
    return $document->importNode($node, TRUE);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLInsertNodes() {
  return array(
    'category' => t('XML'),
    'label' => t('Insert XML child elements'),
    'description' => t('Append a list of XML elements to each of the given XML elements. The new children can be added to either the given context element or to some of its existing children, specified by an XPath query.'),
  );
}

class TfXMLInsertNodes extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode', 'xpath', 'xmlChildNodeList');
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
          return t('The XML element that will be modified, and used as context for the XPath query input.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
    elseif ($inputKey == 'xpath') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath query');

        case 'description':
          return t("The XPath expression specifying the parent element(s) of the new children, relative to the given XML element. By default, the given element itself is being used, so you don't need to assign this value if you just want to add the new children to the element itself.");

        case 'expectedType':
          return 'php:type:string';

        case 'required':
          return FALSE;

        case 'defaultValue':
          return '.'; // XPath for "this element"
      }
    }
    elseif ($inputKey == 'xmlChildNodeList') {
      switch ($propertyKey) {
        case 'label':
          return t('Appended XML elements');

        case 'description':
          return t('The list of XML elements that will be appended to each of the elements resulting from the XPath query.');

        case 'expectedType':
          return 'transformations:list<php:class:DOMNode>';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('modifiedXmlNode');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'modifiedXmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('Modified XML element');

        case 'description':
          return t('A copy of the XML element that was given as input, with the list of children appended to each element in the XPath query results.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $contextNode = $this->input('xmlNode')->data();
    $modifiedNode = $contextNode->cloneNode(TRUE); // TRUE == deep
    $query = $this->input('xpath')->data();

    if ($modifiedNode->nodeType == XML_DOCUMENT_NODE) {
      $document = $modifiedNode;
    }
    else {
      if (strpos($query, '/') === 0 || strpos($query, '..') === 0) {
        $output->setErrorMessage(t('Only XPath queries for descendant elements are allowed.'));
      }
      $document = $modifiedNode->ownerDocument;
    }

    $xpath = new DOMXPath($document);
    $resultNodes = $xpath->query($query, $modifiedNode);

    // Gather the new children in an array so that we don't have to iterate
    // the wrapper's iterator multiple times.
    $childNodes = array();
    $childNodeWrappers = $this->input('xmlChildNodeList')->children(
      TfDataWrapper::ChildrenAlwaysWrapped
    );
    foreach ($childNodeWrappers as $childWrapper) {
      $childNodes[] = TfXMLModificationTools::importNode($document, $childWrapper->data());
    }
    if (empty($childNodes)) { // No changes: return the original node.
      $output->set('modifiedXmlNode', $contextNode);
      return;
    }

    foreach ($resultNodes as $resultNode) {
      foreach ($childNodes as $childNode) {
        $resultNode->appendChild($childNode->cloneNode(TRUE));
      }
    }
    $output->set('modifiedXmlNode', $modifiedNode);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLSetAttributes() {
  return array(
    'category' => t('XML'),
    'label' => t('Set XML attributes'),
    'description' => t('Set a list of XML attributes for each of the given XML elements. The new attributes can be added to either the given context element or to some of its existing children, specified by an XPath query.'),
  );
}

class TfXMLSetAttributes extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode', 'xpath', 'attributeList');
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
          return t('The XML element that will be modified, and used as context for the XPath query input.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
    elseif ($inputKey == 'xpath') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath query');

        case 'description':
          return t("The XPath expression specifying the element(s) where the attributes will be set, relative to the given XML element. By default, the given element itself is being used, so you don't need to assign this value if you just want to set the attributes for the element itself.");

        case 'expectedType':
          return 'php:type:string';

        case 'required':
          return FALSE;

        case 'defaultValue':
          return '.'; // XPath for "this element"
      }
    }
    elseif ($inputKey == 'attributeList') {
      switch ($propertyKey) {
        case 'label':
          return t('Inserted attributes');

        case 'description':
          return t('The list of attributes that will be added to each of the elements resulting from the XPath query. Keys are used as attribute names, and the associated (string) values are used as attribute values.');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('modifiedXmlNode');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'modifiedXmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('Modified XML element');

        case 'description':
          return t('A copy of the XML element that was given as input, with the list of attributes being set for each element in the XPath query results.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $contextNode = $this->input('xmlNode')->data();
    $modifiedNode = $contextNode->cloneNode(TRUE); // TRUE == deep
    $query = $this->input('xpath')->data();

    if ($modifiedNode->nodeType == XML_DOCUMENT_NODE) {
      $document = $modifiedNode;
    }
    else {
      if (strpos($query, '/') === 0 || strpos($query, '..') === 0) {
        $output->setErrorMessage(t('Only XPath queries for descendant elements are allowed.'));
      }
      $document = $modifiedNode->ownerDocument;
    }

    $xpath = new DOMXPath($document);
    $resultNodes = $xpath->query($query, $modifiedNode);

    // Gather the new attributes in an array so that we don't have to iterate
    // the wrapper's iterator multiple times.
    $attributes = array();
    foreach ($this->input('attributeList') as $name => $value) {
      $attributes[$name] = $value;
    }
    if (empty($attributes)) { // No changes: return the original node.
      $output->set('modifiedXmlNode', $contextNode);
      return;
    }

    foreach ($resultNodes as $resultNode) {
      if ($resultNode->nodeType != XML_ELEMENT_NODE) {
        $output->setErrorMessage(t('The XPath query retrieved an XML node that is not an element! Attributes can only be added to elements though, which causes this operation to fail.'));
        return;
      }
      foreach ($attributes as $name => $value) {
        $resultNode->setAttribute($name, $value);
      }
    }
    $output->set('modifiedXmlNode', $modifiedNode);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLRemoveNodes() {
  return array(
    'category' => t('XML'),
    'label' => t('Remove XML child elements'),
    'description' => t('Remove a set of children of the given XML element, the children being specified by an XPath query.'),
  );
}

class TfXMLRemoveNodes extends TfOperation {
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
          return t('The XML element that will be modified, and used as context for the XPath query input.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
    elseif ($inputKey == 'xpath') {
      switch ($propertyKey) {
        case 'label':
          return t('XPath query');

        case 'description':
          return t("The XPath expression specifying the parent element(s) of the new children, relative to the given XML element. By default, the given element itself is being used, so you don't need to assign this value if you just want to add the new children to the element itself.");

        case 'expectedType':
          return 'php:type:string';

        case 'required':
          return FALSE;

        case 'defaultValue':
          return '.'; // XPath for "this element"
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('modifiedXmlNode');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'modifiedXmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('Modified XML element');

        case 'description':
          return t('A copy of the XML element that was given as input, with the specified child elements removed.');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $contextNode = $this->input('xmlNode')->data();
    $modifiedNode = $contextNode->cloneNode(TRUE); // TRUE == deep
    $query = $this->input('xpath')->data();

    if ($modifiedNode->nodeType == XML_DOCUMENT_NODE) {
      $document = $modifiedNode;
    }
    else {
      if (strpos($query, '/') === 0 || strpos($query, '..') === 0) {
        $output->setErrorMessage(t('Only XPath queries for descendant elements are allowed.'));
      }
      $document = $modifiedNode->ownerDocument;
    }

    $xpath = new DOMXPath($document);
    $resultNodes = $xpath->query($query, $modifiedNode);

    foreach ($resultNodes as $resultNode) {
      $resultParent = $resultNode->parentNode;
      $resultParent->removeChild($resultNode);
    }
    $output->set('modifiedXmlNode', $modifiedNode);
  }
}
