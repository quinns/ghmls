<?php
// $Id: operations.pipeline.inc,v 1.14 2009/06/23 19:15:19 jpetso Exp $
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
function transformations_operation_TfPipelineOperation() {
  return array(
    'category' => t('Data flow'),
    'label' => t('Pipeline'),
    'description' => t('Executes a whole pipeline as a single operation.'),
  );
}


/**
 * An operation to execute a TfPipeline. Multiple TfPipelineOperations can run
 * off the same TfPipeline object, but that object is expected not to change
 * while this operation is still in use (otherwise, bugs are likely to appear).
 */
class TfPipelineOperation extends TfOperation implements TfOperationSlotListener {
  private $pipeline;

  /** An array of actual operation instances, one per operationId
   * (which is the array key for each element).
   * NULL if the operations have not yet been instantiated. */
  private $operations;

  /** Cached result of the hasSideEffects() method. */
  private $hasSideEffects;

  /** If pipeline execution failed, this contains the operation identifier
   * of the operation that did not deliver a valid output. */
  private $failedOperationId;


  /**
   * Implementation of TfOperation::initialize().
   */
  public function initialize() {
    $this->unregisterAsOperationSlotListener();
    $this->resetInternalState();
    parent::initialize();
  }

  private function resetInternalState() {
    $this->pipeline = NULL;
    $this->operations = NULL;
    $this->hasSideEffects = NULL;
    $this->failedOperationId = NULL;
  }

  /**
   * Remove this pipeline operation from inner operations' output schema
   * listener lists, if it has previously been added.
   */
  private function unregisterAsOperationSlotListener() {
    if (isset($this->operations)) {
      foreach ($this->operations as $operationId => $operation) {
        $operation->unregisterSlotListener($this);
      }
    }
  }


  /**
   * Return the pipeline that this pipeline operation is based on.
   *
   * Do check the isPipelineValid() method's result before calling this
   * function, as it will throw an exception when the pipeline is not valid.
   * Remember to only access but not change the pipeline as long as this
   * pipeline operation is in active usage.
   */
  public function pipeline() {
    $this->assertPipelineValid();
    return $this->pipeline;
  }

  /**
   * Check whether the pipeline input is set and contains a valid pipeline.
   * If this method returns TRUE, pipeline() will return a pipeline object.
   */
  public function isPipelineValid() {
    if (!isset($this->pipeline) && $this->isInputSet('pipeline')) {
      $pipeline = $this->input('pipeline')->data();

      if (is_string($pipeline) || is_numeric($pipeline)) {
        $this->pipeline = TfPipeline::load($pipeline);
      }
      elseif (is_array($pipeline)) {
        $this->pipeline = TfPipeline::import($pipeline);
      }
      elseif (is_object($pipeline) && $pipeline instanceof TfPipeline) {
        $this->pipeline = $pipeline;
      }

      if (is_object($this->pipeline)) {
        return TRUE;
      }
      // Store the pipeline name or id for error output purposes.
      $this->pipeline = is_array($pipeline) ? $pipeline['name'] : $pipeline;
      return FALSE;
    }
    return is_object($this->pipeline);
  }

  /**
   * Make sure that $this->pipeline is assigned and valid.
   * If you don't know whether the pipeline is (or should be) initialized yet,
   * call isPipelineValid() to make sure this is the case.
   */
  protected function assertPipelineValid() {
    if (!is_object($this->pipeline)) {
      throw new Exception('Pipeline "' . check_plain($this->pipeline) . '" cannot be loaded!');
    }
  }

  /**
   * If pipeline execution failed, this method returns the operation identifier
   * of the operation that did not deliver a valid output. (For successful
   * pipeline executions, FALSE is returned.)
   */
  public function failedOperationId() {
    return isset($this->failedOperationId) ? $this->failedOperationId : FALSE;
  }

  /**
   * Retrieve an operation from this pipeline as TfOperation object.
   *
   * Operation inputs and data types are pre-assigned as good as the
   * implementation allows. (Obviously, data that is only created during the
   * execution of the pipeline cannot be assigned as input at this point.)
   *
   * An exception is thrown when the given operation id does not correspond to
   * an operation in the pipeline.
   */
  public function operation($operationId) {
    $this->assertPipelineValid();
    $this->ensureOperationInstances();

    if (!isset($this->operations[$operationId])) {
      $operation = $this->pipeline->createOperation($operationId);
      foreach ($this->pipeline->sources($operationId) as $inputKey => $source) {
        if (isset($source['data'])) {
          $operation->setInput($inputKey, $source['data']);
        }
      }
      return $operation;
    }
    return $this->operations[$operationId];
  }

  /**
   * Overriding TfOperation::hasSideEffects().
   */
  public function hasSideEffects() {
    if (isset($this->hasSideEffects)) {
      return $this->hasSideEffects;
    }
    $this->ensureOperationInstances();
    $this->hasSideEffects = FALSE;

    foreach ($this->operations as $operationId => $operation) {
      if ($operation->hasSideEffects()) {
        $this->hasSideEffects = TRUE;
        break;
      }
    }
    return $this->hasSideEffects;
  }

  /**
   * Pipelines are potentially very complex, so let's cache the output.
   */
  protected function isOutputCachingEnabled() {
    return TRUE;
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    $keys = array('pipeline');

    if ($this->isPipelineValid()) {
      foreach (array_keys($this->pipeline->targets(TfPipeline::Parameter)) as $key) {
        $keys[] = 'p:' . $key;
      }
    }
    return $keys;
  }

  /**
   * Overriding TfOperation::inputChanged().
   */
  protected function inputChanged($inputKey, $previousValue) {
    if ($inputKey == 'pipeline') {
      if ($this->isInputSet($inputKey) && isset($previousValue)) {
        $newPipeline = $this->input($inputKey)->data();
        $oldPipeline = $previousValue->data();

        if (is_numeric($newPipeline) && is_numeric($oldPipeline) && $newPipeline == $oldPipeline) {
          return; // no changes
        }
      }
    }
    parent::inputChanged($inputKey, $previousValue);

    if ($inputKey == 'pipeline') {
      $this->resetInternalState();
      $changedKeys = array_diff(array('pipeline'), $this->inputKeys());
      $this->updateInputSchema($changedKeys);
      $this->updateOutputSchema($this->outputKeys());
    }
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'pipeline') {
      switch ($propertyKey) {
        case 'label':
          if ($this->isInputSet($inputKey) && !$this->isPipelineValid()) {
            return t('Invalid pipeline!');
          }
          return t('Pipeline');

        case 'description':
          if ($this->isInputSet($inputKey) && !$this->isPipelineValid()) {
            return t('The currently assigned pipeline cannot be loaded, please assign a valid one to this input.');
          }
          return t('The pipeline that should be executed as operation. Determines which inputs and outputs are available.');

        case 'expectedType':
          return 'transformations:pipeline';

        default:
          return NULL;
      }
    }
    else {
      $this->assertPipelineValid();
      $this->ensureOperationInstances();
      $parameterKey = substr($inputKey, 2); // remove the 'p:' prefix
      $targets = $this->pipeline->targets(TfPipeline::Parameter, $parameterKey);

      if (empty($targets)) {
        throw new Exception('Invalid parameter key: ' . $parameterKey);
      }
      foreach ($targets as $targetOperationId => $targetDataSpecs) {
        foreach ($targetDataSpecs as $targetInputKey => $true) {
          $targetOperation = $this->operations[$targetOperationId];
          $targetKey = $targetInputKey;
          break; // A pipeline parameter always corresponds to a single target.
        }
      }
      return $targetOperation->inputProperty($targetKey, $propertyKey);
    }
  }

  /**
   * Overriding TfOperation::inputTypeChanged() in order to relay the new
   * data type to the inner operations and at the same time narrow down output
   * schema change notifications (which are handled by the
   * observedOutputSchemaChanged() implementation).
   */
  protected function inputTypeChanged($inputKey) {
    if ($inputKey == 'pipeline') {
      return; // We don't need to act on that.
    }
    $this->assertPipelineValid();
    $parameterKey = substr($inputKey, 2); // remove the 'p:' prefix
    $targets = $this->pipeline->targets(TfPipeline::Parameter, $parameterKey);
    $type = $this->inputProperty($parameterKey, 'assignedType');
    $this->ensureOperationInstances();

    foreach ($targets as $targetOperationId => $targetDataSpecs) {
      foreach ($targetDataSpecs as $targetKey => $true) {
        $this->operations[$targetOperationId]->setInputType($targetKey, $type);
      }
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    if ($this->isPipelineValid()) {
      return array_keys($this->pipeline->sources(TfPipeline::Output));
    }
    return array();
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    $this->assertPipelineValid();
    $this->ensureOperationInstances();
    $source = $this->pipeline->source(TfPipeline::Output, $outputKey);

    if (empty($source)) {
      throw new Exception('Invalid output key: ' . $outputKey);
    }
    return $this->operations[$source['entity']]->outputProperty($source['key'], $propertyKey);
  }

  /**
   * Implementation of TfOperationSlotListener::observedInputSchemaChanged().
   */
  public function observedInputChanged(TfOperation $changedOperation, $inputKey) {
    // Nothing to do.
  }

  /**
   * Implementation of TfOperationSlotListener::observedInputSchemaChanged().
   */
  public function observedInputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges) {
    // Nothing to do.
  }

  /**
   * Implementation of TfOperationSlotListener::observedOutputSchemaChanged().
   */
  public function observedOutputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges) {
    foreach ($this->operations as $operationId => $operation) {
      if ($changedOperation === $operation) {
        $changedKeys = array();
        $existingKeys = array_merge($keyChanges->updated, $keyChanges->added);

        foreach ($this->pipeline->targets($operationId) as $outputKey => $targets) {
          foreach ($targets as $targetOperationId => $targetDataSpecs) {
            foreach ($targetDataSpecs as $targetKey => $true) {
              if ($targetOperationId == TfPipeline::Output) {
                if (in_array($outputKey, $keyChanges->updated)) {
                  $changedKeys[] = $targetKey;
                }
              }
              else { // it's an operation
                if (in_array($outputKey, $existingKeys)) {
                  $type = $operation->outputProperty($outputKey, 'expectedType');
                  $this->operations[$targetOperationId]->setInputType($targetKey, $type);
                }
                elseif (in_array($outputKey, $keyChanges->removed)) {
                  $this->operations[$targetOperationId]->setInputType($targetKey, NULL);
                }
              }
            }
          }
        }
        if (!empty($changedKeys)) {
          $this->updateOutputSchema($changedKeys);
        }
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    // Set up our execution environment.
    $this->assertPipelineValid();
    $this->ensureOperationInstances();

    foreach ($this->pipeline->targets(TfPipeline::Parameter) as $parameterKey => $targets) {
      foreach ($targets as $targetOperationId => $targetDataSpecs) {
        foreach ($targetDataSpecs as $targetKey => $true) {
          $inputKey = 'p:' . $parameterKey;
          $this->operations[$targetOperationId]->setInput($targetKey, $this->input($inputKey));
        }
      }
    }

    // Continue to run until all scheduled operations have been executed.
    foreach ($this->operations as $currentOperationId => $operation) {
      // Get the object for the currently processed operation.
      if (!isset($this->operations[$currentOperationId])) {
        throw new Exception('No operation exists with operation identifier ' .
          $currentOperationId . '.');
      }
      $currentOperation = $this->operations[$currentOperationId];

      // If the operation output is not relevant, we might just as well
      // skip executing the operation in the first place.
      ///TODO: move to executionOrder()?.
      /*if (!$currentOperation->hasSideEffects()) {
        $targets = $this->pipeline->targets($currentOperationId);
        if (empty($targets)) {
          continue; // neither side effects nor targets: skip
        }
      }*/

      // Execute the operation and retrieve its output.
      $currentOperationOutput = $currentOperation->output();
      if (!$currentOperationOutput->isValid()) {
        $this->tearDown();
        $this->failedOperationId = $currentOperationId;
        $output->setErrorMessage($currentOperationOutput->errorMessage());
        return;
      }

      // Assign the output data to the connected pipeline outputs and/or
      // other operation inputs.
      foreach ($this->pipeline->targets($currentOperationId) as $sourceOutputKey => $targets) {
        foreach ($targets as $targetEntity => $targetDataSpecs) {
          foreach ($targetDataSpecs as $targetKey => $true) {
            $dataWrapper = $currentOperationOutput->at($sourceOutputKey);

            if ($targetEntity == TfPipeline::Output) {
              $output->set($targetKey, $dataWrapper);
            }
            else { // it's an operation
              $this->operations[$targetEntity]->setInput($targetKey, $dataWrapper);
            }
          }
        }
      }
    }

    $this->tearDown();
  }

  private function ensureOperationInstances() {
    $this->assertPipelineValid();

    if (isset($this->operations)) {
      return; // Has already been called before, nothing to do, move on.
    }
    $this->operations = array();

    // Create all operations, already in the correct order of execution.
    foreach ($this->pipeline->executionOrder() as $operationId) {
      $this->operations[$operationId] = $this->pipeline->createOperation($operationId);
    }

    // Register as output schema listener in order to make schema changes
    // propagating correctly when input data is changed.
    foreach ($this->operations as $operationId => &$operation) {
      $operation->registerSlotListener($this);
    }
  }

  private function tearDown() {
    $this->operations = NULL;
  }
}
