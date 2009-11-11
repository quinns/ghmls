<?php
// $Id: transformations.transformations.inc,v 1.6 2009/06/23 19:21:34 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Implementation of hook_transformations_data_wrapper_info():
 * Return a registry of all the data wrappers provided by this module.
 */
function transformations_transformations_data_wrapper_info() {
  $dataWrappers = array();

  $dataWrappers['TfConcreteData'] = array(
    // Included by default, no need to provide a file path.
  );
  $dataWrappers['TfArrayData'] = array(
    'file' => 'data.array.inc',
    'parent' => 'TfConcreteData',
  );
  $dataWrappers['TfObjectData'] = array(
    'file' => 'data.object.inc',
    'parent' => 'TfConcreteData',
  );
  return $dataWrappers;
}

/**
 * Implementation of hook_transformations_operation_info():
 * Return a registry of all the operations provided by this module.
 */
function transformations_transformations_operation_info() {
  $operations = array();
  $path = drupal_get_path('module', 'transformations') . '/transformations';

  $operations['TfPipelineOperation'] = array(
    'file' => 'operations.pipeline.inc',
  );
  $operations['TfForEach'] = array(
    'file' => 'operations.foreach.inc',
  );
  $operations['TfDirAndBaseNamesFromFilePath'] = array(
    'file' => 'operations.file.inc',
  );
  $operations['TfWholeStringAtOnceFromFile'] = array(
    'file' => 'operations.file.inc',
  );
  $operations['TfTextLinesFromFile'] = array(
    'file' => 'operations.file.inc',
  );
  $operations['TfPHPEvaluate'] = array(
    'file' => 'operations.eval.inc',
  );
  $operations['TfListExtractSingleItem'] = array(
    'file' => 'operations.list.inc',
  );
  $operations['TfSimpleMappingOutputsFromStructure'] = array(
    'file' => 'operations.mapping.inc',
  );
  $operations['TfObjectTreeFromJSON'] = array(
    'file' => 'operations.json.inc',
  );
  $operations['TfArrayTreeFromJSON'] = array(
    'file' => 'operations.json.inc',
  );
  $operations['TfDumpData'] = array(
    'file' => 'operations.debug.inc',
  );
  $operations['TfDumpDataToDrupalMessages'] = array(
    'parent' => 'TfDumpData',
    'file' => 'operations.debug.inc',
  );
  $operations['TfDumpDataToPlaintext'] = array(
    'parent' => 'TfDumpData',
    'file' => 'operations.debug.inc',
  );

  return $operations;
}
