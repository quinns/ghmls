<?php
// $Id: transformations_xml.transformations.inc,v 1.5 2009/06/27 15:05:44 jpetso Exp $
/**
 * @file
 * XML Transformations - Transformations for processing XML data.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Implementation of hook_transformations_data_wrapper_info():
 * Return a registry of all the data wrappers provided by this module.
 */
function transformations_xml_transformations_data_wrapper_info() {
  $dataWrappers = array();

  $dataWrappers['TfXMLNodeData'] = array(
    'file' => 'data.xml.inc',
    'parent' => 'TfConcreteData',
  );
  return $dataWrappers;
}

/**
 * Implementation of hook_transformations_operation_info():
 * Return a registry of all the operations provided by this module.
 */
function transformations_xml_transformations_operation_info() {
  $operations = array();
  $path = drupal_get_path('module', 'transformations_xml') . '/transformations';

  $operations['TfXMLNodeFromXMLString'] = array(
    'file' => 'operations.xml.convert.inc',
  );
  $operations['TfXMLNodeFromXMLFile'] = array(
    'file' => 'operations.xml.convert.inc',
  );
  $operations['TfOuterXMLStringFromXMLNode'] = array(
    'file' => 'operations.xml.convert.inc',
  );
  $operations['TfInnerXMLStringFromXMLNode'] = array(
    'file' => 'operations.xml.convert.inc',
  );
  $operations['TfXMLTagInformation'] = array(
    'file' => 'operations.xml.extract.inc',
  );
  $operations['TfXMLXPathQuery'] = array(
    'file' => 'operations.xml.extract.inc',
  );
  $operations['TfXMLXPathEvaluate'] = array(
    'file' => 'operations.xml.extract.inc',
  );
  $operations['TfXMLInsertNodes'] = array(
    'file' => 'operations.xml.modify.inc',
  );
  $operations['TfXMLSetAttributes'] = array(
    'file' => 'operations.xml.modify.inc',
  );
  $operations['TfXMLRemoveNodes'] = array(
    'file' => 'operations.xml.modify.inc',
  );

  return $operations;
}
