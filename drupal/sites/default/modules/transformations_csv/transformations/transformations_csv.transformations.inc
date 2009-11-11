<?php
// $Id: transformations_csv.transformations.inc,v 1.2 2009/06/01 19:46:08 jpetso Exp $
/**
 * @file
 * CSV Transformations -
 * Transformations for processing CSV (Comma Separated Values) data.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Implementation of hook_transformations_operation_info():
 * Return a registry of all the operations provided by this module.
 */
function transformations_csv_transformations_operation_info() {
  $operations = array();

  $operations['TfRecordsFromCSVText'] = array(
    'file' => 'operations.csv.inc',
  );
  $operations['TfRecordsFromCSVTextLines'] = array(
    'file' => 'operations.csv.inc',
  );
  $operations['TfCSVTextFromRecords'] = array(
    'file' => 'operations.csv.inc',
  );
  $operations['TfCSVTextLineFromRecord'] = array(
    'file' => 'operations.csv.inc',
  );
  return $operations;
}
