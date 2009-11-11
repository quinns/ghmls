<?php
// $Id: operations.xml.convert.inc,v 1.3 2009/06/27 13:59:58 jpetso Exp $
/**
 * @file
 * XML Transformations - Transformations for processing XML data.
 *
 * This file contains operations for converting between XML strings and
 * internal XML element objects.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLNodeFromXMLString() {
  return array(
    'category' => t('XML'),
    'label' => t('XML string to XML element object'),
    'description' => t('Transforms a valid XML string into a tree structure, enabling easier access of the XML elements.'),
  );
}

class TfXMLNodeFromXMLString extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlString');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlString') {
      switch ($propertyKey) {
        case 'label':
          return t('XML string');

        case 'expectedType':
          return 'php:type:string';

        case 'dataWidgetTypeHints':
          return array('string:multiline' => TRUE);
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('xmlNode');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML document element');

        case 'expectedType':
          return 'php:class:DOMDocument';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    try {
      TfDataWrapper::includeWrapper('TfXMLNodeData');
      $xmlWrapper = new TfXMLNodeData($this->input('xmlString')->data());
    }
    catch (Exception $e) {
      $output->setErrorMessage(t('Unable to parse XML string: !message', array(
        '!message' => $e->getMessage(),
      )));
      return;
    }
    $output->set('xmlNode', $xmlWrapper);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfXMLNodeFromXMLFile() {
  return array(
    'category' => t('XML'),
    'label' => t('XML file to XML element object'),
    'description' => t('Transforms a valid XML file into a tree structure, enabling easier access of the XML elements.'),
  );
}

class TfXMLNodeFromXMLFile extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('filepath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'filepath') {
      switch ($propertyKey) {
        case 'label':
          return t('File path');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('xmlNode');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML document element');

        case 'expectedType':
          return 'php:class:DOMDocument';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $doc = new DOMDocument();
    $filename = realpath(trim($this->input('filepath')->data()));
    if ($doc->load($filename, LIBXML_NOCDATA | LIBXML_NOENT | LIBXML_NOBLANKS)) {
      $output->set('xmlNode', $doc);
    }
    else {
      $output->setErrorMessage(t('Unable to load XML file !filename.', array(
        '!filename' => $filename,
      )));
    }
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfOuterXMLStringFromXMLNode() {
  return array(
    'category' => t('XML'),
    'label' => t('XML element object to XML string'),
    'description' => t('Transforms an XML element object into a valid XML string, including the XML header.'),
  );
}

class TfOuterXMLStringFromXMLNode extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML element');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('xmlString');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'xmlString') {
      switch ($propertyKey) {
        case 'label':
          return t('XML string');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    TfDataWrapper::includeWrapper('TfXMLNodeData'); // for TfXMLTools
    $node = $this->input('xmlNode')->data();
    $output->set('xmlString', TfXMLTools::asXMLDocument($node));
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_xml_operation_TfInnerXMLStringFromXMLNode() {
  return array(
    'category' => t('XML'),
    'label' => t('XML element object to inner XML string'),
    'description' => t('Extracts the contents of an XML element object into a simple string. You probably want to use this in order to make '),
  );
}

class TfInnerXMLStringFromXMLNode extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('xmlNode');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'xmlNode') {
      switch ($propertyKey) {
        case 'label':
          return t('XML element');

        case 'expectedType':
          return 'php:class:DOMNode';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('stringContents');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'stringContents') {
      switch ($propertyKey) {
        case 'label':
          return t('String contents');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    TfDataWrapper::includeWrapper('TfXMLNodeData'); // for TfXMLTools
    $node = $this->input('xmlNode')->data();
    $output->set('stringContents', TfXMLTools::innerXML($node));
  }
}
