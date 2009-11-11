<?php
// $Id: operations.debug.inc,v 1.4 2009/06/23 19:15:19 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfDumpData() {
  return array(
    'abstract' => TRUE,
    'category' => t('Debugging'),
    'label' => t('Dump data'),
    'description' => t('Writes the contents of the input data to the output specified by the respective subclass.'),
  );
}

abstract class TfDumpData extends TfOperation {
  private $debugOutput;
  private $indentation;

  /**
   * Overriding TfOperation::hasSideEffects() to return TRUE.
   */
  public function hasSideEffects() {
    return TRUE;
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('data');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'data') {
      switch ($propertyKey) {
        case 'label':
          return t('Data');

        case 'expectedType':
          return 'transformations:any';
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array();
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    return NULL;
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $this->debugOutput = '';
    $this->indentation = 0;
    $this->constructDebugOutput($this->input('data'));
    $this->dump($this->debugOutput, $output);
  }

  abstract protected function dump($debugOutput, TfOutput $operationOutput);

  private function constructDebugOutput($data) {
    if (!is_object($data) || !($data instanceof TfDataWrapper)) {
      $this->debugOutput .= $this->printR($data);
    }
    elseif (!$data->isIteratable()) {
      $this->debugOutput .= $this->printR($data->data());
    }
    else {
      $this->debugOutput .= "\n";
      foreach ($data as $label => $child) {
        $this->debugOutput .= $this->indented($label . ' {');
        ++$this->indentation;
        $this->constructDebugOutput($child);
        $this->debugOutput .= "}\n";
        --$this->indentation;
      }
      --$this->indentation;
      $this->debugOutput .= $this->indented('');
      ++$this->indentation;
    }
  }

  private function indented($text) {
    if ($this->indentation == 0) {
      return $text;
    }
    $lines = explode("\n", $text);
    foreach (array_keys($lines) as $key) {
      for ($i = 0; $i < $this->indentation; $i++) {
        $lines[$key] = '    ' . $lines[$key];
      }
    }
    return implode("\n", $lines);
  }

  private function printR($data) {
    if (is_string($data)) {
      if (strpos($data, "\n") === FALSE) {
        return ' "' . $data . '" ';
      }
      $lines = explode("\n", $data);
      $data = "\n\"" . implode("\"\n\"", $lines) ."\"";
    }
    elseif (is_bool($data)) {
      $data = ($data) ? 'TRUE' : 'FALSE';
    }
    else {
      $data = print_r($data, TRUE);
    }

    if (strpos($data, "\n") === FALSE) {
      return ' ' . $data . ' ';
    }
    $lines = explode("\n", $data);
    $first_line = array_shift($lines);
    $data = $first_line . "\n" . $this->indented(implode("\n", $lines));
    --$this->indentation;
    $data .= $this->indented("\n");
    ++$this->indentation;
    return $data;
  }
}

/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfDumpDataToDrupalMessages() {
  return array(
    'category' => t('Debugging'),
    'label' => t('Dump data to status message'),
    'description' => t('Writes the contents of the input data to a Drupal status message.'),
  );
}

class TfDumpDataToDrupalMessages extends TfDumpData {
  protected function dump($debugOutput, TfOutput $operationOutput) {
    drupal_set_message('<pre>' . check_plain($debugOutput) . '</pre>');
  }
}

/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfDumpDataToPlaintext() {
  return array(
    'category' => t('Debugging'),
    'label' => t('Dump data to plaintext'),
    'description' => t('Writes the contents of the input data as string to the "plaintext" output of this operation.'),
  );
}

class TfDumpDataToPlaintext extends TfDumpData {
  /**
   * Overriding TfDumpData::hasSideEffects() back to returning FALSE.
   */
  public function hasSideEffects() {
    return FALSE;
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('plaintext');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'plaintext') {
      switch ($propertyKey) {
        case 'label':
          return t('Data as plaintext');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  protected function dump($debugOutput, TfOutput $operationOutput) {
    $operationOutput->set('plaintext', $debugOutput);
  }
}
