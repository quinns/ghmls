<?php
// $Id: operations.list.inc,v 1.6 2009/06/23 19:15:19 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2008 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfListExtractSingleItem() {
  return array(
    'category' => t('Lists and structures'),
    'label' => t('Extract single item from list'),
    'description' => t('Retrieves the first (or second, or third, ...) item from a list.'),
  );
}

class TfListExtractSingleItem extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('list', 'itemNumber');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'list') {
      switch ($propertyKey) {
        case 'label':
          return t('Item list');

        case 'description':
          return t('The list of items from which a child element should be extracted.');

        case 'expectedType':
          return 'transformations:list<transformations:any>';
      }
    }
    elseif ($inputKey == 'itemNumber') {
      switch ($propertyKey) {
        case 'label':
          return t('Item number');

        case 'description':
          return t('The "howmanyeth" item in the list should be extracted, starting with 1 (which is the default value).');

        case 'required':
          return FALSE;

        case 'defaultValue':
          return 1;

        case 'expectedType':
          return 'php:type:number';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('item');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'item') {
      switch ($propertyKey) {
        case 'label':
          return t('Extracted item');

        case 'expectedType':
          return 'transformations:any';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $listIterator = $this->input('list')->children();
    $listIterator->rewind();
    $itemNumber = $this->input('itemNumber')->data();

    for ($i = $itemNumber; ($i > 1) && $listIterator->valid(); $i--) {
      $listIterator->next();
    }
    if (!$listIterator->valid()) {
      $output->setErrorMessage(format_plural($itemNumber,
        'The given list is empty, so no item can be extracted.',
        'The given list does not contain @count items, so that item cannot be extracted.'
      ));
      return;
    }
    $output->set('item', $listIterator->current());
  }
}