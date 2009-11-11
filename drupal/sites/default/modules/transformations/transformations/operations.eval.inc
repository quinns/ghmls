<?php
// $Id: operations.eval.inc,v 1.10 2009/06/23 19:15:19 jpetso Exp $
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
function transformations_operation_TfPHPEvaluate() {
  return array(
    'category' => t('Other'),
    'label' => t('Evaluate PHP code'),
    'description' => t('Executes code on the parameter(s). Not typesafe or malicious-user-proof, use at your own risk.'),
  );
}

class TfPHPEvaluate extends TfOperation {
  private $inputCount;
  private $outputCount;

  /**
   * Implementation of TfOperation::inputs().
   */
  public function initialize() {
    $this->inputCount = $this->inputInfo('inputCount', 'defaultValue');
    $this->outputCount = $this->inputInfo('outputCount', 'defaultValue');
    parent::initialize();
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    $inputs = array('inputCount', 'outputCount', 'code');

    for ($i = 0; $i < $this->inputCount; ++$i) {
      $inputs[] = 'input' . $i;
    }
    return $inputs;
  }

  /**
   * Overriding TfOperation::inputChanged().
   */
  protected function inputChanged($inputKey, $previousValue) {
    if ($inputKey == 'inputCount' || $inputKey == 'outputCount') {
      // Both provide default values, so no need to check for isset().
      if ($this->input($inputKey)->data() == $previousValue->data()) {
        return; // no changes
      }
    }
    parent::inputChanged($inputKey, $previousValue);

    if ($inputKey == 'inputCount') {
      $this->inputCount = $this->input('inputCount')->data();
      $this->updateInputSchema();
    }
    if ($inputKey == 'outputCount') {
      $this->outputCount = $this->input('outputCount')->data();
      $this->updateOutputSchema();
    }
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'inputCount') {
      switch ($propertyKey) {
        case 'label':
          return t('Number of inputs');

        case 'description':
          return t('The number of input slots that can be assigned to this operation.');

        case 'expectedType':
          return 'php:type:number';

        case 'required':
          return FALSE;

        case 'defaultValue':
          return 1;
      }
    }
    if ($inputKey == 'outputCount') {
      switch ($propertyKey) {
        case 'label':
          return t('Number of outputs');

        case 'description':
          return t('The number of output slots that this operation will calculate.');

        case 'expectedType':
          return 'php:type:number';

        case 'required':
          return FALSE;

        case 'defaultValue':
          return 1;
      }
    }
    elseif ($inputKey == 'code') {
      switch ($propertyKey) {
        case 'label':
          return t('PHP code');

        case 'description':
          $description = t('The PHP code that is executed.');

          if ($this->inputCount > 0) {
            $inputs = array();
            for ($i = 0; $i < $this->inputCount; ++$i) {
              $inputs[] = '$input[' . $i . ']';
            }
            $description .= "\n\n" . t('Available input values are: !inputs. Those variables are TfDataWrapper objects - if they wrap directly accessible data then you can access that data by calling $input[$i]->data() (which works for strings, numbers, etc.), whereas if the wrapper represents a list then you can traverse it with a simple foreach loop on $input[$i].', array('!inputs' => implode(', ', $inputs))) . "\n";
          }

          if ($this->outputCount > 0) {
            $outputs = array();
            for ($i = 0; $i < $this->outputCount; ++$i) {
              $outputs[] = '$output[' . $i . ']';
            }
            $description .= "\n" . t('All output values (!outputs) must be assigned a non-NULL value, otherwise the operation will fail with an error.', array('!outputs' => implode(', ', $outputs)));
          }

          return trim($description);

        case 'expectedType':
          return 'php:type:string';

        case 'dataWidgetTypeHints':
          return array('string:multiline' => TRUE);
      }
    }
    switch ($propertyKey) {
      case 'label':
        return t('$input[!num]', array('!num' => substr($inputKey, 5)));

      case 'expectedType':
        return 'transformations:any';
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    $outputs = array();
    for ($i = 0; $i < $this->outputCount; ++$i) {
      $outputs[] = 'output' . $i;
    }
    return $outputs;
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    switch ($propertyKey) {
      case 'label':
        return t('$output[!num]', array('!num' => substr($outputKey, 6)));

      case 'expectedType':
        return 'transformations:any';
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $_eval_op_output) {
    $input = array();
    for ($i = 0; $i < $this->inputCount; ++$i) {
      $input[$i] = $this->input('input' . $i);
      if (!$input[$i]->isIteratable()) {
        $input[$i] = $input[$i]->data();
      }
    }
    unset($inputCount);
    $output = array();

    // Do the locomotion!
    eval($this->input('code')->data());

    for ($i = 0; $i < $this->outputCount; ++$i) {
      if (!array_key_exists($i, $output)) {
        $_eval_op_output->setErrorMessage(t('$output[!num] was not set by the evaluated PHP code.', array('!num' => $i)));
        return;
      }
      $_eval_op_output->set('output' . $i, $output[$i]);
    }
  }
}
