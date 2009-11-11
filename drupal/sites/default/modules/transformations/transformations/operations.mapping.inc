<?php
// $Id: operations.mapping.inc,v 1.7 2009/06/23 19:15:19 jpetso Exp $
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
function transformations_operation_TfSimpleMappingOutputsFromStructure() {
  return array(
    'category' => t('Lists and structures'),
    'label' => t('Extract child elements from structure (simple mapping)'),
    'description' => t('Provides child elements of an item structure as separate outputs. Translation is done by a simple source key to output name map.'),
  );
}

class TfSimpleMappingOutputsFromStructure extends TfOperation {
  private $map; // a cached, array-ified version of the 'map' input

  /**
   * Overriding TfOperation::initialize().
   */
  public function initialize() {
    $this->map = array();
    parent::initialize();
  }

  protected function initializeMap() {
    $this->map = array();

    if (!$this->isInputSet('map')) {
      return;
    }
    foreach ($this->input('map') as $sourceKey => $outputName) {
      $this->map[$sourceKey] = $outputName;
    }
  }

  /**
   * Called when input data has been (re-)set using setInput().
   */
  protected function inputChanged($inputKey, $previousValue) {
    parent::inputChanged($inputKey, $previousValue);

    if ($inputKey == 'map') {
      $this->initializeMap();
      $this->updateOutputSchema();
    }
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('map', 'structure');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'map') {
      switch ($propertyKey) {
        case 'label':
          return t('Key map');

        case 'description':
          return t('An association of source keys in the input structure (given as the keys of this "key map" list) to output names (given as the values of the key map).');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
    elseif ($inputKey == 'structure') {
      switch ($propertyKey) {
        case 'label':
          return t('Item structure');

        case 'description':
          return t('The structure (list of items) whose child elements you want to extract.');

        case 'expectedType':
          return 'transformations:structure<transformations:any ...>';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array_keys($this->map);
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    switch ($propertyKey) {
      case 'label':
        return empty($this->map[$outputKey])
          ? t('(empty)')
          : $this->map[$outputKey];

      case 'expectedType':
        try {
          $inputType = $this->inputProperty('structure', 'assignedType');
          return TfDataType::extractTypeParameter($inputType,
            'transformations:structure<transformations:any ... T>', 'T'
          );
        }
        catch (Exception $e) {
          return 'transformations:any';
        }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    foreach ($this->input('structure') as $sourceKey => $data) {
      if (isset($this->map[$sourceKey])) {
        $output->set($sourceKey, $data);
      }
    }
  }
}
