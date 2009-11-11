<?php
// $Id: operations.csv.inc,v 1.7 2009/06/23 19:08:47 jpetso Exp $
/**
 * @file
 * CSV Transformations -
 * Transformations for processing CSV (Comma Separated Values) data.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

// Also see http://www.rfc-editor.org/rfc/rfc4180.txt which describes the
// standardized version of the CSV format. PHP's fgetcsv() and str_getcsv()
// functions do not quite conform to that standard because they use an
// escape character ('\') instead of using a doubled double-quote ('""') for
// double-quote escapings inside a quoted field.
//
// Despite the massive amount of example CSV parsers in the PHP documentation's
// user comment section, none seem to handle this standard with all its traits
// (quote escaping, quoted fields spanning multiple lines, etc.) correctly
// and in a readable way. So here's another custom implementation. (Bah.)


/**
 * Helper class for providing implementations for standard input/output info.
 */
class TfCSVConnectionInfoHelper {
  public static function csvTextInfo($propertyKey) {
    switch ($propertyKey) {
      case 'label':
        return t('CSV text');

      case 'expectedType':
        return 'php:type:string';

      case 'dataWidgetTypeHints':
        return array('string:multiline' => TRUE);
    }
  }

  public static function delimiterInfo($propertyKey) {
    switch ($propertyKey) {
      case 'required':
        return FALSE;

      case 'label':
        return t('Delimiter');

      case 'defaultValue':
        return ',';

      case 'expectedType':
        return 'php:type:string';
    }
  }

  public static function skipFirstLineInfo($propertyKey) {
    switch ($propertyKey) {
      case 'required':
        return FALSE;

      case 'label':
        return t('Skip first line');

      case 'defaultValue':
        return FALSE;

      case 'expectedType':
        return 'php:type:boolean';
    }
  }

  public static function columnNamesInfo($propertyKey) {
    switch ($propertyKey) {
      case 'required':
        return FALSE;

      case 'label':
        return t('Column names');

      case 'defaultValue':
        return array();

      case 'expectedType':
        return 'transformations:list<php:type:string>';
    }
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_csv_operation_TfRecordsFromCSVText() {
  return array(
    'category' => t('CSV'),
    'label' => t('CSV text to list of records'),
    'description' => t('Transforms plaintext CSV (comma separated values) data into a list of records, each record consisting of the fields in the respective text line.'),
  );
}

class TfRecordsFromCSVText extends TfOperation {
  private $lineTransformation;

  /**
   * Overriding TfOperation::initialize().
   */
  public function initialize() {
    $this->lineTransformation = TfOperation::load('TfRecordsFromCSVTextLines');
    parent::initialize();
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('csvText', 'delimiter', 'skipFirstLine', 'columnNames');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'csvText') {
      return TfCSVConnectionInfoHelper::csvTextInfo($propertyKey);
    }
    elseif ($inputKey == 'delimiter') {
      return TfCSVConnectionInfoHelper::delimiterInfo($propertyKey);
    }
    elseif ($inputKey == 'skipFirstLine') {
      return TfCSVConnectionInfoHelper::skipFirstLineInfo($propertyKey);
    }
    elseif ($inputKey == 'columnNames') {
      return TfCSVConnectionInfoHelper::columnNamesInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return $this->lineTransformation->outputs();
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    return $this->lineTransformation->outputInfo($outputKey, $propertyKey);
  }

  /**
   * Overriding TfOperation::inputChanged().
   */
  protected function inputChanged($inputKey, $previousValue) {
    parent::inputChanged($inputKey, $previousValue);

    if ($key != 'csvText') {
      $this->lineTransformation->setInput($key, $this->input($key));
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $lines = explode("\n", $this->input('csvText')->data());
    $this->lineTransformation->setInput('csvTextLines', $lines);
    $lineOutput = $this->lineTransformation->output();

    if (!$lineOutput->isValid()) {
      $output->setErrorMessage($lineOutput->errorMessage());
      return;
    }
    foreach ($lineOutput as $key => $data) {
      $output->set($key, $data);
    }
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_csv_operation_TfRecordsFromCSVTextLines() {
  return array(
    'category' => t('CSV'),
    'label' => t('CSV text lines to list of records'),
    'description' => t('Transforms a list of text lines in CSV (comma separated values) format into a list of records, each record consisting of the fields in the respective text line.'),
  );
}

class TfRecordsFromCSVTextLines extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('csvTextLines', 'delimiter', 'skipFirstLine', 'columnNames');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'csvTextLines') {
      switch ($propertyKey) {
        case 'label':
          return t('CSV text lines');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
    elseif ($inputKey == 'delimiter') {
      return TfCSVConnectionInfoHelper::delimiterInfo($propertyKey);
    }
    elseif ($inputKey == 'skipFirstLine') {
      return TfCSVConnectionInfoHelper::skipFirstLineInfo($propertyKey);
    }
    elseif ($inputKey == 'columnNames') {
      return TfCSVConnectionInfoHelper::columnNamesInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('records');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'records') {
      switch ($propertyKey) {
        case 'label':
          return t('List of records');

        case 'expectedType':
          $columnNames = array();
          foreach ($this->input('columnNames') as $columnName) {
            $columnNames[] = $columnName;
          }

          if (!empty($columnNames)) {
            return 'transformations:list<transformations:structure<' .
              implode(', ', $columnNames) . '>>';
          }
          return 'transformations:list<transformations:list<php:type:string>>';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $columnNames = array();

    foreach ($this->input('columnNames') as $columnName) {
      $columnNames[] = $columnName;
    }
    module_load_include('php', 'transformations_csv', 'CSVParser');

    $parser = new CSVParser();
    $parser->setDelimiter($this->input('delimiter')->data());
    $parser->setSkipFirstLine($this->input('skipFirstLine')->data());
    $parser->setColumnNames(empty($columnNames) ? FALSE : $columnNames);
    $rows = $parser->parse($this->input('csvTextLines')->children());

    $output->set('records', $rows);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_csv_operation_TfCSVTextFromRecords() {
  return array(
    'category' => t('CSV'),
    'label' => t('List of records to CSV text'),
    'description' => t('Transforms a list of records - each record consisting of a fixed number of string fields - into plaintext CSV (comma separated values) data.'),
  );
}

class TfCSVTextFromRecords extends TfOperation {
  private $lineTransformation;

  /**
   * Overriding TfOperation::initialize().
   */
  public function initialize() {
    $this->lineTransformation = TfOperation::load('TfCSVTextLineFromRecord');
    parent::initialize();
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('records', 'delimiter', 'skipFirstLine', 'columnNames');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'records') {
      switch ($propertyKey) {
        case 'label':
          return t('List of records');

        case 'expectedType':
          $recordType = $this->lineTransformation->inputProperty(
            'record', 'expectedType'
          );
          return 'transformations:list<' . $recordType . '>';
      }
    }
    elseif ($inputKey == 'delimiter') {
      return TfCSVConnectionInfoHelper::delimiterInfo($propertyKey);
    }
    elseif ($inputKey == 'skipFirstLine') {
      return TfCSVConnectionInfoHelper::skipFirstLineInfo($propertyKey);
    }
    elseif ($inputKey == 'columnNames') {
      return TfCSVConnectionInfoHelper::columnNamesInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('csvText');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'csvText') {
      return TfCSVConnectionInfoHelper::csvTextInfo($propertyKey);
    }
  }

  /**
   * Overriding TfOperation::inputChanged().
   */
  protected function inputChanged($key) {
    if ($key == 'delimiter') {
      $this->lineTransformation->setInput($key, $this->input($key));
    }
    parent::inputChanged($key);
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $rows = array();
    $csvText = '';

    if ($this->input('skipFirstLine')->data() == TRUE) {
      // Insert the row names as additional line at the start of the array.
      $this->lineTransformation->setInput('record', $this->input('columnNames'));
      $lineOutput = $this->lineTransformation->output();

      if (!$lineOutput->isValid()) {
        $output->setErrorMessage($lineOutput->errorMessage());
        return;
      }
      $csvText .= $lineOutput->at('csvText') . "\n";
    }
    foreach ($this->input('records') as $row) {
      $this->lineTransformation->setInput('record', $row);
      $lineOutput = $this->lineTransformation->output();

      if (!$lineOutput->isValid()) {
        $output->setErrorMessage($lineOutput->errorMessage());
        return;
      }
      $csvText .= $lineOutput->at('csvText')->data() . "\n";
    }
    $output->set('csvText', $csvText);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_csv_operation_TfCSVTextLineFromRecord() {
  return array(
    'category' => t('CSV'),
    'label' => t('List of strings to CSV text line'),
    'description' => t('Transforms a single record - consisting of a number of string fields - into plaintext CSV (comma separated values) data. The resulting text is a single line in most cases, except if a field contained a line break by itself.'),
  );
}

/**
 * A transformation to get plaintext CSV data for a single row (or line)
 * out of a string array. Not trailed by line breaks.
 */
class TfCSVTextLineFromRecord extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('record', 'delimiter');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'record') {
      switch ($propertyKey) {
        case 'label':
          return t('Record (list of strings)');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
    elseif ($inputKey == 'delimiter') {
      return TfCSVConnectionInfoHelper::delimiterInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('csvText');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'csvText') {
      return TfCSVConnectionInfoHelper::csvTextInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $fields = array();

    foreach ($this->input('record') as $key => $field) {
      // Stick everything into double quotes and escape the real double quotes
      // by duplicating them.
      $fields[$key] = '"' . strtr($field, array('"' => '""')) . '"';
    }
    $output->set('csvText', implode($this->input('delimiter')->data(), $fields));
  }
}
