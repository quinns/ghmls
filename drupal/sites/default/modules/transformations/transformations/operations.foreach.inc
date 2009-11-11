<?php
// $Id: operations.foreach.inc,v 1.9 2009/06/23 22:19:47 jpetso Exp $
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
function transformations_operation_TfForEach() {
  return array(
    'category' => t('Data flow'),
    'label' => t('For-each loop'),
    'description' => t('Executes an operation for each element of a list.'),
  );
}


/**
 * An operation that takes a list of data elements and executes the given
 * inner operation once for each of those elements. Possible other inputs of
 * the inner operation are passed from the foreach operation without being
 * unfolded from any list.
 */
class TfForEach extends TfOperation implements TfOperationSlotListener {
  /** The instance of the operation that is executed for each element.
   * NULL if the operation has not yet been instantiated. */
  private $innerOperation;


  /**
   * Implementation of TfOperation::initialize().
   */
  public function initialize() {
    $this->unregisterAsOperationSlotListener();
    $this->innerOperation = NULL;
    parent::initialize();
  }

  /**
   * Remove this foreach operation from the inner operation's output schema
   * listener lists, if it has previously been added.
   */
  private function unregisterAsOperationSlotListener() {
    if (isset($this->innerOperation) && is_object($this->innerOperation)) {
      $this->innerOperation->unregisterSlotListener($this);
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
  public function innerOperation() {
    $this->assertInnerOperationValid();
    return $this->innerOperation;
  }

  /**
   * Check whether the pipeline input is set and contains a valid pipeline.
   * If this method returns TRUE, pipeline() will return a pipeline object.
   */
  public function isInnerOperationValid() {
    if (!isset($this->innerOperation) && $this->isInputSet('innerOperation')) {
      // Assign the inner operation, newly instantiating it if necessary.
      $innerOperationClass = $this->input('innerOperation')->data();
      $this->innerOperation = TfOperation::load($innerOperationClass);

      if (is_object($this->innerOperation)) {
        // Register ourselves with any schema change notifications.
        $this->innerOperation->registerSlotListener($this);
        return TRUE;
      }
      // Store the class name for error output purposes.
      $this->innerOperation = $innerOperationClass;
      return FALSE;
    }
    return is_object($this->innerOperation);
  }

  /**
   * Make sure that $this->pipeline is assigned and valid.
   * If you don't know whether the pipeline is (or should be) initialized yet,
   * call isPipelineValid() to make sure this is the case.
   */
  protected function assertInnerOperationValid() {
    if (!is_object($this->innerOperation)) {
      throw new Exception('Inner operation "' . check_plain($this->innerOperation) . '" cannot be loaded!');
    }
  }

  /**
   * Overriding TfOperation::hasSideEffects().
   */
  public function hasSideEffects() {
    if ($this->isInnerOperationValid()) {
      return $this->innerOperation->hasSideEffects();
    }
    return FALSE; // nevermind, without inner op we can never execute anyways
  }

  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    $keys = array('innerOperation');

    if ($this->isInnerOperationValid()) {
      $listInputSet = $this->isInputSet('listInput');
      $listInput = $listInputSet ? $this->input('listInput')->data() : NULL;

      foreach ($this->innerOperation->inputKeys() as $key) {
        if ($listInputSet && $key == $listInput) {
          $keys[] = 'l:' . $key;
        }
        else {
          $keys[] = 'i:' . $key;
        }
      }
      if (!$listInputSet) {
        // We still want a list input, even if it won't be assigned to any
        // input of the inner operation.
        $keys[] = 'itemList';
      }
      $keys[] = 'listInput';
      $keys[] = 'extractedOutput';
    }
    return $keys;
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'innerOperation') {
      switch ($propertyKey) {
        case 'label':
          if ($this->isInputSet($inputKey) && !$this->isInnerOperationValid()) {
            return t('Invalid inner operation!');
          }
          return t('Inner operation');

        case 'description':
          if ($this->isInputSet($inputKey) && !$this->isInnerOperationValid()) {
            return t('The currently assigned inner operation cannot be loaded, please assign a valid one to this input.');
          }
          return t('The operation that will be executed once for each value in a list.');

        case 'expectedType':
          return 'transformations:operation';

        default:
          return NULL;
      }
    }
    // All other inputs depend on the inner operation being correctly loaded.
    $this->assertInnerOperationValid();

    if ($inputKey == 'listInput') {
      switch ($propertyKey) {
        case 'label':
          return t('List input');

        case 'description':
          return t('The inner operation\'s input that will be assigned an item list instead of a single input value. (The foreach operation will be executed once for each item in that list.) If you need to assign fixed values to the inner operation in order to fix its input schema, please do that prior to selecting the list input property.');

        case 'expectedType':
          return 'php:type:string';

        case 'dataWidgetTypeHints':
          $options = array();
          foreach ($this->innerOperation->inputKeys() as $inputKey) {
            $options[$inputKey] =
              check_plain($this->innerOperation->inputProperty($inputKey, 'label'));
          }
          return array('options' => $options);

        case 'required':
          return FALSE;

        default:
          return NULL;
      }
    }
    if ($inputKey == 'extractedOutput') {
      switch ($propertyKey) {
        case 'label':
          return t('Extracted output');

        case 'description':
          return t('By default, all of the inner operation\'s outputs will be returned in a structure for each list item. But sometimes you are only interested in a single output, and you want a list of the values returned by this output. If that is the case, you can select an output to extract by setting this option.');

        case 'expectedType':
          return 'php:type:string';

        case 'dataWidgetTypeHints':
          $options = array();
          foreach ($this->innerOperation->outputKeys() as $outputKey) {
            $options[$outputKey] =
              check_plain($this->innerOperation->outputProperty($outputKey, 'label'));
          }
          return array('options' => $options);

        case 'required':
          return FALSE;

        default:
          return NULL;
      }
    }
    elseif (strpos($inputKey, 'i:') === 0) {
      // All inputs except for the list input are simply relayed with no change.
      $innerInputKey = substr($inputKey, 2);
      return $this->innerOperation->inputProperty($innerInputKey, $propertyKey);
    }
    elseif ($inputKey == 'itemList') {
      switch ($propertyKey) {
        // List input slot if no specific list input has been assigned.
        case 'label':
          return t('Item list');

        case 'description':
          return t('The inner operation will be executed once for each item in this list. As no list input is currently defined, these items will not be relayed to the inner operation, they just serve as counter.');

        case 'expectedType':
          return 'transformations:list<transformations:any>';
      }
    }
    elseif (strpos($inputKey, 'l:') === 0) {
      // List input slot if a specific list input has been assigned.
      $innerInputKey = substr($inputKey, 2);

      switch ($propertyKey) {
        case 'label':
          $label = $this->innerOperation->inputProperty($innerInputKey, 'label');
          return t('!element list', array('!element' => $label));

        case 'description':
          $label = $this->innerOperation->inputProperty($innerInputKey, 'label');
          $description = t('A list of "!element" values. The inner operation will be executed once for each item in this list, and the item will be assigned to the operation\'s "!element" input.', array('!element' => $label));

          $innerDescription = $this->innerOperation->inputProperty($innerInputKey, 'description');
          if (!empty($innerDescription)) {
            $description .= t("!list-description The inner operation describes this input like this:\n\n!inner-description", array('!element' => $label));
          }
          return $description;

        case 'expectedType':
          $innerType = $this->innerOperation->inputProperty($innerInputKey, 'expectedType');
          return 'transformations:list<' . $innerType . '>';
      }
    }
  }

  /**
   * Called when input data has been (re-)set using setInput().
   */
  protected function inputChanged($inputKey, $previousValue) {
    if ($inputKey == 'innerOperation' || $inputKey == 'listInput') {
      if ($this->isInputSet($inputKey) && isset($previousValue)
          && $this->input($inputKey)->data() == $previousValue->data()) {
        return; // no changes
      }
    }
    parent::inputChanged($inputKey, $previousValue);

    if ($inputKey == 'innerOperation') {
      $this->innerOperation = NULL;
      $changedKeys = array_diff(array('innerOperation'), $this->inputKeys());
      $this->updateInputSchema($changedKeys);
      $this->updateOutputSchema('outputElements');
      return;
    }
    $this->assertInnerOperationValid();

    if ($inputKey == 'listInput') {
      if ($this->isInputSet('listInput')) {
        // Unset any current value for this input.
        $this->innerOperation->setInput($this->input('listInput')->data(), NULL);
      }
      $previousListInput = isset($previousValue) ? 'l:' . $previousValue->data() : 'itemList';
      $this->updateInputSchema($previousListInput);
    }
    // Relay all inputs except for the list input which is being
    // handled separately in execute().
    elseif (strpos($inputKey, 'i:') === 0) {
      $innerInputKey = substr($inputKey, 2);
      $this->innerOperation->setInput($innerInputKey, $this->input($inputKey));
    }
  }

  /**
   * Overriding TfOperation::inputTypeChanged() in order to relay the new
   * data type to the inner operation and at the same time narrow down output
   * schema change notifications (which are handled by the
   * observedOutputSchemaChanged() implementation).
   */
  protected function inputTypeChanged($inputKey) {
    if (in_array($inputKey, array('innerOperation', 'listInput', 'itemList'))) {
      // Nothing to relay, nothing will change in the schema.
      return;
    }
    $this->assertInnerOperationValid();
    $type = $this->inputProperty($inputKey, 'assignedType');

    if (strpos($inputKey, 'l:') === 0) {
      $innerInputKey = substr($inputKey, 2);

      // The outer operation gets a list element set, so we need to extract its
      // child element(s) that should be passed to the inner operation.
      try {
        $elementType = TfDataType::extractTypeParameter($type,
          'transformations:list<transformations:any T>', 'T'
        );
        $this->innerOperation->setInputType($innerInputKey, $elementType);
      }
      catch (Exception $e) {
        // Something went wrong while trying to determine the element type,
        // so let's ignore this event and stick with the bare minimum: calling
        // the default implementation.
        parent::inputTypeChanged($inputKey);
      }
    }
    elseif (strpos($inputKey, 'i:') === 0) {
      $innerInputKey = substr($inputKey, 2);
      $this->innerOperation->setInputType($innerInputKey, $type);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    if (!$this->isInnerOperationValid()) {
      return array();
    }
    return array('outputElements');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    $this->assertInnerOperationValid();

    if ($outputKey == 'outputElements') {
      switch ($propertyKey) {
        case 'label':
          return t('List of outputs');

        case 'expectedType':
          // Wrap the original outputs in a structure collection (which is the
          // original output schema), and the set of all structures in
          // another list (which is the foreach operation's output schema).
          $innerOutputKeys = $this->innerOperation->outputKeys();
          $innerOutputTypes = array();

          foreach ($innerOutputKeys as $innerKey) {
            $innerOutputTypes[] = $this->innerOperation->outputProperty($innerKey, 'expectedType');
          }

          return 'transformations:list<transformations:structure<'
            . implode(',', $innerOutputTypes) . '>>';
      }
    }
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
    // $changedOperation must always be $this->innerOperation, because
    // we don't register ourselves with any other operation.
    // So no need to check which operation is given as $changedOperation.
    $changedKeys = $keyChanges->updated;
    $listInput = $this->isInputSet('listInput') ? $this->input('listInput')->data() : NULL;
    foreach ($changedKeys as &$key) {
      $key = (isset($listInput) && $key == $listInput) ? ('l:' . $key) : ('i:' . $key);
    }
    $this->updateInputSchema($changedKeys);
  }

  /**
   * Implementation of TfOperationSlotListener::observedOutputSchemaChanged().
   */
  public function observedOutputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges) {
    // $changedOperation must always be $this->innerOperation, because
    // we don't register ourselves with any other operation.
    // So no need to check which operation is given as $changedOperation.
    if ($this->isInputSet('extractedOutput')
        && in_array($this->input('extractedOutput')->data(), $keyChanges->removed)) {
      $this->setInput('extractedOutput', NULL);
    }
    $this->updateOutputSchema('outputElements');
  }

  /**
   * Implementation of TfOperation::execute().
   * Sports two different implementations (instant or deferred execution),
   * depending on whether the inner operation has side effects or not.
   */
  protected function execute(TfOutput $output) {
    $this->assertInnerOperationValid();

    $listInput = $this->isInputSet('listInput')
      ? 'l:' . $this->input('listInput')->data()
      : 'itemList';
    $innerListInput = $this->isInputSet('listInput')
      ? $this->input('listInput')->data()
      : NULL;
    $extractedOutput = $this->isInputSet('extractedOutput')
      ? $this->input('extractedOutput')->data()
      : NULL;

    if ($this->innerOperation->hasSideEffects()) {
      // Variant 1: Less efficient, but more immediate.
      $outputElements = array();

      foreach ($this->input($listInput) as $key => $data) {
        if (isset($innerListInput)) {
          $this->innerOperation->setInput($innerListInput, $data);
        }
        $outputElements[$key] = isset($extractedOutput)
          ? $this->innerOperation->output()->at($extractedOutput)
          : $this->innerOperation->output();
      }
      if (isset($innerListInput)) {
        $this->innerOperation->setInput($innerListInput, NULL); // Done, reset.
      }
      $output->set('outputElements', $outputElements);
    }
    else {
      // Variant 2: This is just a bit fake, as we defer execution until the
      // actual results are requested from the data wrapper itself. Haha.
      $data = (object) array(
        'innerOperation' => clone $this->innerOperation, // clone == fixate inputs
        'listInputKey' => $innerListInput,
        'listInputData' => $this->input($listInput),
        'extractedOutput' => $extractedOutput,
      );
      $wrapper = TfDataWrapper::create(new TfForEachIterator($data));
      $output->set('outputElements', $wrapper);
    }
  }
}

class TfForEachIterator extends IteratorIterator {
  private $innerOperation;
  private $listInputKey;
  private $extractedOutput;

  public function __construct($data) {
    $this->innerOperation = $data->innerOperation;
    $this->listInputKey = $data->listInputKey;
    $this->extractedOutput = $data->extractedOutput;

    parent::__construct($data->listInputData->children(TfDataWrapper::ChildrenMixed));
  }

  public function current() {
    if (isset($this->listInputKey)) {
      $currentListElement = parent::current();
      $this->innerOperation->setInput($this->listInputKey, $currentListElement);
    }
    return isset($this->extractedOutput)
      ? $this->innerOperation->output()->at($this->extractedOutput)
      : $this->innerOperation->output();

    // ...you may ask, why can we return a TfOutput object as value?
    // It goes like this: TfOutput is an object, so when being wrapped,
    // it will end up in a TfObjectData wrapper. When rawChildElementIterator()
    // is called, TfObjectData returns the TfOutput object itself because it
    // is an iterator. The combination of all of that enables tree navigation
    // by just returning a traversable object. Cool, eh?
  }
}
