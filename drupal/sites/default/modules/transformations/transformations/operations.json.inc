<?php
// $Id: operations.json.inc,v 1.5 2009/06/23 19:15:19 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Helper class for providing implementations for standard input/output info.
 */
class TfJSONConnectionInfoHelper {
  public static function jsonStringInfo($propertyKey) {
    switch ($propertyKey) {
      case 'label':
        return t('JSON string');

      case 'expectedType':
        return 'php:type:string';

      case 'dataWidgetTypeHints':
        return array('string:multiline' => TRUE);
    }
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfObjectTreeFromJSON() {
  return array(
    'category' => t('JSON'),
    'label' => t('JSON to object tree'),
    'description' => t('Transforms a valid JSON tree into a PHP object tree structure.'),
  );
}

class TfObjectTreeFromJSON extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('jsonString');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'jsonString') {
      return TfJSONConnectionInfoHelper::jsonStringInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('objectTree');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'objectTree') {
      switch ($propertyKey) {
        case 'label':
          return t('Object tree');

        case 'expectedType':
          return 'php:class:stdClass';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $json = $this->input('jsonString')->data();
    $tree = json_decode($json);

    if (!is_object($tree)) {
      $output->setErrorMessage(t("Unable to parse the JSON string:\n!json", array(
        '!json' => $json,
      )));
      return;
    }
    $output->set('objectTree', $tree);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfArrayTreeFromJSON() {
  return array(
    'category' => t('JSON'),
    'label' => t('JSON to array tree'),
    'description' => t('Transforms a valid JSON tree into a PHP array tree structure.'),
  );
}

class TfArrayTreeFromJSON extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('jsonString');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'jsonString') {
      return TfJSONConnectionInfoHelper::jsonStringInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('arrayTree');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'arrayTree') {
      switch ($propertyKey) {
        case 'label':
          return t('Array tree');

        case 'expectedType':
          return 'php:type:array';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $json = $this->input('jsonString')->data();
    $tree = json_decode($json, TRUE); // TRUE is for the associative array structure.

    if (!is_array($tree)) {
      $output->setErrorMessage(t("Unable to parse the JSON string:\n!json", array(
        '!json' => $json,
      )));
      return;
    }
    $output->set('arrayTree', $tree);
  }
}
