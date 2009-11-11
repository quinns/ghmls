<?php
// $Id: transformations.operation.inc,v 1.8 2009/06/12 16:00:12 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * An instance of the TfOutput class is returned by TfOperation::output(),
 * and is used to gather all outputs of an operation in a nicely accessible
 * and safe enclosure.
 */
class TfOutput implements IteratorAggregate {
  private $outputKeys;
  private $operation;
  private $outputs;
  private $errorMessage;

  final public function __construct(TfOperation $operation) {
    $this->outputKeys = $operation->outputKeys();
    $this->operation = $operation;
    $this->outputs = array();
    $this->errorMessage = NULL;
  }

  /**
   * Return TRUE if the operation was successful and all outputs have been set,
   * or FALSE otherwise.
   */
  final public function isValid() {
    return isset($this->outputs);
  }

  /**
   * Make sure the operation is valid (@see TfOutput::isValid()), and throw
   * an exception if that is not the case.
   */
  private function assertValid() {
    if (!$this->isValid()) {
      $message = 'Operation ' . $this->operation->uiName() .
        ' was unsuccessful, no values can be retrieved with TfOutput::at().';
      if (!empty($this->errorMessage)) {
        $message .= ' ' . $this->errorMessage;
      }
      throw new Exception($message);
    }
  }

  /**
   * To be called by the operation in order to assign data to its outputs.
   * Must not be called after the operation has been executed.
   */
  final public function set($outputKey, $data) {
    if (!isset($this->outputKeys)) {
      throw new Exception('TfOutput::set() must not be called after the operation has been executed.');
    }
    if (!in_array($outputKey, $this->outputKeys)) {
      throw new Exception('Output key "' . $outputKey .
        '" was not specified by the output schema.');
    }
    if (!is_object($data) || !($data instanceof TfDataWrapper)) {
      $data = TfDataWrapper::create($data);
    }
    $this->outputs[$outputKey] = $data;
  }

  /**
   * Define the operation as failed, and specify a error message that explains
   * what went wrong. The message should not be run through output filters
   * like check_plain(), as the user of the class is expected to do that.
   */
  final public function setErrorMessage($errorMessage) {
    $this->operationDone(empty($errorMessage) ? t('(unknown error)') : $errorMessage);
  }

  /**
   * Retrieve the error message in case the operation has failed (which is the
   * case if the isValid() method returns FALSE). The message has not been run
   * through output filters like check_plain(), so the caller is expected to
   * care for proper sanitization of the error message. For a successfully
   * executed operation, no error message is set and this method returns FALSE.
   */
  final public function errorMessage() {
    return $this->isValid() ? FALSE : $this->errorMessage;
  }

  /**
   * Retrieve the keys for all output values. These keys can be passed to the
   * at() method to retrieve the corresponding output values.
   */
  final public function keys() {
    return array_keys($this->outputs);
  }

  /**
   * Retrieve the output value for the given @p $outputKey. Must not be called
   * if the isValid() method returns FALSE.
   */
  final public function at($outputKey) {
    $this->assertValid();

    if (!isset($this->outputs[$outputKey])) {
      throw new Exception('Data was not set for output key "' . $outputKey . '".');
    }
    return $this->outputs[$outputKey];
  }

  /**
   * Implementation of IteratorAggregate::getIterator().
   */
  final public function getIterator() {
    $this->assertValid();
    return new ArrayIterator($this->outputs);
  }

  /**
   * Finish up the creation of output values by checking whether all expected
   * outputs have actually been set. Called by TfOperation::output() after the
   * operation has been executed, or by TfOutput::setErrorMessage().
   */
  final public function operationDone($errorMessage = NULL) {
    if (isset($this->errorMessage)) {
      return; // This function has already been called before.
    }
    if (isset($errorMessage)) {
      // Set values so that isValid() == FALSE.
      unset($this->outputKeys);
      unset($this->outputs);
      $this->errorMessage = $errorMessage;
      return;
    }
    $outputs = array(); // for putting the outputs into correct order

    foreach ($this->outputKeys as $outputKey) {
      if (!isset($this->outputs[$outputKey])) {
        // Set values so that isValid() == FALSE.
        unset($this->outputKeys);
        unset($this->outputs);
        $this->errorMessage = t('Output key @key was not set by the operation.',
                                array('@key' => $outputKey));
        return;
      }

      // Output has been set, add it to the result array.
      if (is_object($this->outputs[$outputKey]) && ($this->outputs[$outputKey] instanceof TfDataWrapper)) {
        $outputs[$outputKey] = $this->outputs[$outputKey];
      }
      else {
        $outputs[$outputKey] = TfDataWrapper::create($this->outputs[$outputKey]);
      }
    }
    $this->outputs = $outputs;

    // Successful! Unset the stuff that we won't need anymore.
    unset($this->operation);
    unset($this->outputKeys);
  }
}

/**
 * Classes can implement this interface in order to get notified about changes
 * in the input and output schemas of an operation, as well as about input
 * value changes (all of which are most probably triggered by input changes in
 * the observed operation). In order to get notified, the object has to be
 * registered with the operation by calling TfOperation::registerSlotListener().
 */
interface TfOperationSlotListener {
  /**
   * Called by a TfOperation when one of its input values is assigned a
   * new value.
   *
   * @param $changedOperation
   *   The operation that changed part or all of its input schema.
   * @param $inputKey
   *   An array of keys that were left untouched.
   */
  public function observedInputChanged(TfOperation $changedOperation, $inputKey);

  /**
   * Called by a TfOperation when its input schema changes.
   *
   * @param $changedOperation
   *   The operation that changed part or all of its input schema.
   * @param $remainingKeys
   *   An array of keys that were left untouched.
   */
  public function observedInputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges);

  /**
   * Called by a TfOperation when its output schema changes.
   *
   * @param $changedOperation
   *   The operation that changed part or all of its output schema.
   * @param $keyChanges
   *   An object describing which parts of the schema changed.
   */
  public function observedOutputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges);
}


/**
 * Base class for any data operation, with an arbitrary number of other
 * operations assigned for input retrieval and a single TfDataWrapper object
 * as output value.
 */
abstract class TfOperation {
  private $input;
  private $inputInfo;
  private $outputCache;
  private $outputInfo;
  private $slotListeners;

  //
  // Static methods.
  //

  /**
   * Retrieve a new instance of the given operation, defined by its $className.
   */
  public static function load($className) {
    $operations = transformations_operation_info();
    if (!isset($operations[$className]) || !empty($operations[$className]['abstract'])) {
      return FALSE;
    }
    _transformations_include_extension($className, $operations);
    return new $className();
  }


  //
  // Instance methods.
  //

  /**
   * Create and initialize a new instance of this TfOperation (sub)class.
   *
   * @see TfOperation::initialize()
   */
  final public function __construct() {
    $this->initialize();
  }

  /**
   * How the operation prefers to be called in the user interface.
   * Note that this name is not run through check_plain() or similar output
   * sanitization functions, the caller is expected to take care of that.
   *
   * Note that the name returned by this method is not necessarily used,
   * it's just a hint to the API user. By default, the 'label' property is
   * returned, which was (hopefully) specified in the operation-specific
   * implementation of [module]_operation_[class]().
   */
  public function uiName() {
    $info = transformations_operation_info(get_class($this));
    return ($info && isset($info['label'])) ? $info['label'] : get_class($this);
  }

  /**
   * Initialize this object. This is called from within the constructor but
   * can also be called to reset the object to its initial state.
   */
  public function initialize() {
    // Our own initializations.
    $this->slotListeners = new SplObjectStorage();
    $this->input = array();
    $this->clearOutputCache();
    $this->inputInfo = array_fill_keys($this->inputs(), array());
    $this->outputInfo = array_fill_keys($this->outputs(), array());
  }


  /**
   * @defgroup connection-info Input/output info
   * @{
   * Methods for retrieving information about expected input and output slots
   * and how the corresponding data is supposed to look like, as well as
   * methods for providing input and output schema information to the operation.
   */

  /**
   * Return an array of possible input keys for this operation., i.e. slots
   * that can be assigned input data. Among other things, these can be passed
   * to the setInput() method as key.
   *
   * Further information like the input's user-visible name, the expected
   * data type for the assigned data or whether the input is required or not
   * can be retrieved using the inputProperty() method.
   */
  final public function inputKeys() {
    return array_keys($this->inputInfo);
  }

  /**
   * Return an array of possible input keys for this operation.
   * Basically, this is the uncached version of inputKeys().
   */
  abstract protected function inputs();

  /**
   * Return an array of possible output keys for this operation., i.e. results
   * that can be retrieved from the TfOutput object that is returned by the
   * output() method.
   *
   * Further information like the output's user-visible name or description for
   * the output data can be retrieved using the outputProperty() method.
   */
  final public function outputKeys() {
    return array_keys($this->outputInfo);
  }

  /**
   * Return an array of possible input keys for this operation.
   * Basically, this is the uncached version of outputKeys().
   */
  abstract protected function outputs();


  /**
   * Return details on a specific operation input specified by @p $inputKey.
   * (If there is no such input key, an exception will be thrown. So make sure
   * that the inputKeys() method actually includes that key.)
   *
   * The @p $propertyKey parameter specifies which type of details should
   * be retrieved. Operations can provide any additional values, however the
   * following property keys are mandatory for any operation to provide
   * (or are provided by TfOperation itself) and thus will also be accessible:
   *
   * - 'label': A user-visible name for the input slot.
   * - 'required': If TRUE, the input must always be set with setInput().
   *      If FALSE, it can be skipped and a default value will be used instead.
   * - 'defaultValue': Retrieve the default input data for a given key (wrapped
   *      in a TfDataWrapper). NULL can be returned if the input is mandatory
   *      and there is no default value for this key.
   * - 'expectedType': The data type that is expected for the input value,
   *      similar (and corresponding) to the one given by TfDataWrapper::type(),
   *      but the type given here may be extended with (potentially nested)
   *      type parameters (e.g. 'transformations:list<php:type:string>',
   *      'transformations:structure<php:type:int, php:class:DateTime>').
   *      'transformations:any' can be used to specify that any value will be
   *      accepted, and by naming a type parameter, an input type will be
   *      propagated to the same type/name combination in the output schema
   *      (e.g. 'transformations:list<transformations:any Item>' might become
   *      'transformations:list<php:type:string>').
   *      If the input value does not conform to this data type, the operation
   *      behavior is undefined.
   * - 'assignedType': If a more detailed input type (with type parameters
   *      filled in) has been provided using the setInputType() method, it will
   *      be returned here. Otherwise, the 'expectedType' property key itself
   *      will be returned. (Not fully implemented yet, currently returns
   *      'expectedType' in all cases.)
   * - 'dataWidgetTypeHints': An array with type specific elements that might
   *      (but not necessarily will) be interpreted by data widgets.
   *
   * A 'description' (string) property is not mandatory, but operations are
   * encouraged to provide one. If no description is provided, an empty string
   * is returned.
   *
   * If no property could be retrieved for the given @p $propertyKey,
   * NULL is returned.
   */
  final public function inputProperty($inputKey, $propertyKey) {
    $this->assertInputKeyExpected($inputKey);

    if (!array_key_exists($propertyKey, $this->inputInfo[$inputKey])) {
      // Special case: the only property handled directly by TfOperation.
      if ($propertyKey == 'assignedType') {
        if (isset($this->inputInfo[$inputKey]['assignedType'])) {
          return $this->inputInfo[$inputKey]['assignedType'];
        }
        $propertyKey = 'expectedType';
      }

      // Retrieve the value from our subclass, and cache it in $this->inputInfo.
      $value = $this->inputInfo($inputKey, $propertyKey);

      if (!isset($value)) {
        if ($propertyKey == 'required') {
          $value = TRUE; // By default, inputs are required.
        }
        elseif ($propertyKey == 'label') {
          $value = $inputKey; // Should make for a bearable default label.
        }
        elseif ($propertyKey == 'description') {
          $value = ''; // In order to battle lots of PHP notices.
        }
        elseif ($propertyKey == 'expectedType') {
          throw new Exception('The ' . $this->uiName() .
            ' operation did not specify a data type for the input key ' .
            $inputKey . ' although that property is mandatory.');
        }
        elseif ($propertyKey == 'dataWidgetTypeHints') {
          $value = array();
        }
      }
      elseif ($propertyKey == 'defaultValue') { // && isset($value), from the elseif
        if (!(is_object($value) && $value instanceof TfDataWrapper)) {
          $value = TfDataWrapper::create($value);
        }
      }
      $this->inputInfo[$inputKey][$propertyKey] = $value;
    }
    return $this->inputInfo[$inputKey][$propertyKey];
  }

  /**
   * Return details on a specific operation input specified by @p $inputKey.
   * This function is only called by TfOperation::inputProperty(), and
   * essentially is the uncached version of that function. In order to avoid
   * bad surprises, the inputProperty() method makes sure that no other
   * @p $inputKey can be passed than those returned by inputs()/inputKeys().
   *
   * The @p $propertyKey parameter specifies which type of details should
   * be retrieved. Operations can provide any additional values, TfOperation
   * defines a couple of properties by itself:
   *
   * - 'label': A user-visible name for the input slot. Optional for the
   *      operation to provide - if NULL is returned by this function,
   *      inputProperty() will simply set the label to @p $inputKey.
   * - 'description': A longer (possibly multi-line) description of the
   *      input slot. HTML formatting won't be applied, so stick with
   *      plaintext and "\n" linebreaks. Optional for the operation to provide.
   * - 'required': If TRUE, the input must always be set with setInput().
   *      If FALSE, it can be skipped and a default value will be used instead.
   *      Optional for the operation to provide - if NULL is returned by this
   *      function then inputProperty() will set the input to be required.
   * - 'defaultValue': Must be implemented by the subclass for mandatory inputs:
   *      Retrieve the default input data for a given key (either wrapped in a
   *      TfDataWrapper or not, TfOperation copes with both).
   * - 'expectedType': Must be implemented by the subclass: The data type that
   *      is expected for the input value, similar (and corresponding) to the
   *      one given by TfDataWrapper::type(), but the type given here may be
   *      extended with (potentially nested) type parameters
   *      (e.g. 'transformations:list<php:type:string>',
   *      'transformations:structure<php:type:int, php:class:DateTime>').
   *      'transformations:any' can be used to specify that any value will be
   *      accepted, and by naming a type parameter, an input type will be
   *      propagated to the same type/name combination in the output schema
   *      (e.g. 'transformations:list<transformations:any Item>' might become
   *      'transformations:list<php:type:string>').
   *      If the input value does not conform to this data type, the operation
   *      behavior is undefined.
   * - 'dataWidgetTypeHints': An array with type specific elements that might
   *      (but not necessarily will) be interpreted by data widgets.
   *
   * A 'description' (string) property is not mandatory, but operations are
   * encouraged to provide one.
   *
   * If the operation doesn't define a value for the given @p $propertyKey,
   * NULL is returned.
   */
  abstract protected function inputInfo($inputKey, $propertyKey);

  /**
   * Return details on a specific operation output specified by @p $outputKey.
   * (If there is no such output key, an exception will be thrown. So make sure
   * that the outputKeys() method actually includes that key.)
   *
   * The @p $propertyKey parameter specifies which type of details should
   * be retrieved. Operations can provide any additional values, however the
   * following property keys are mandatory for any operation to provide
   * (or are provided by TfOperation itself) and thus will also be accessible:
   *
   * - 'label': A user-visible name for the output slot.
   * - 'expectedType': The data type that is expected for the output value,
   *      similar (and corresponding) to the one given by TfDataWrapper::type(),
   *      but the type given here may be extended with (potentially nested)
   *      type parameters (e.g. 'transformations:list<php:type:string>',
   *      'transformations:structure<php:type:int, php:class:DateTime>').
   *      'transformations:any' can be used to specify that any value will be
   *      accepted, and by naming a type parameter, an input type will be
   *      propagated to the same type/name combination in the output schema
   *      (e.g. 'transformations:list<transformations:any Item>' might become
   *      'transformations:list<php:type:string>').
   *      The operation needs to ensure that the value actually conforms to
   *      this data type.
   *
   * A 'description' (string) property is not mandatory, but operations are
   * encouraged to provide one.
   *
   * If no property could be retrieved for the given @p $propertyKey,
   * NULL is returned.
   */
  final public function outputProperty($outputKey, $propertyKey) {
    $this->assertOutputKeyExpected($outputKey);

    if (!array_key_exists($propertyKey, $this->outputInfo[$outputKey])) {
      // Retrieve the value from our subclass, and cache it in $this->outputInfo.
      $value = $this->outputInfo($outputKey, $propertyKey);

      if (!isset($value)) {
        if ($propertyKey == 'label') {
          $value = $outputKey; // Should make for a bearable default label.
        }
        elseif ($propertyKey == 'description') {
          $value = ''; // In order to battle lots of PHP notices.
        }
        elseif ($propertyKey == 'expectedType') {
          throw new Exception('The ' . $this->uiName() .
            ' operation did not specify a data type for the output key ' .
            $outputKey . ' although that property is mandatory.');
        }
      }
      $this->outputInfo[$outputKey][$propertyKey] = $value;
    }
    return $this->outputInfo[$outputKey][$propertyKey];
  }

  /**
   * Return details on a specific operation output specified by @p $outputKey.
   * This function is only called by TfOperation::outputProperty(), and
   * essentially is the uncached version of that function. In order to avoid
   * bad surprises, the outputProperty() method makes sure that no other
   * @p $outputKey can be passed than those returned by outputKeys().
   *
   * The @p $propertyKey parameter specifies which type of details should
   * be retrieved. Operations can provide any additional values, TfOperation
   * defines a couple of properties by itself:
   *
   * - 'label': A user-visible name for the output slot. Optional for the
   *      operation to provide - if NULL is returned by this function,
   *      outputProperty() will simply set the label to @p $outputKey.
   * - 'description': A longer (possibly multi-line) description of the
   *      output slot. HTML formatting won't be applied, so stick with
   *      plaintext and "\n" linebreaks. Optional for the operation to provide.
   * - 'expectedType': Only mandatory property key for operations:
   *      The data type that is expected for the output value, similar
   *      (and corresponding) to the one given by TfDataWrapper::type(),
   *      but the type given here may be extended with (potentially nested)
   *      type parameters (e.g. 'transformations:list<php:type:string>',
   *      'transformations:structure<php:type:int, php:class:DateTime>').
   *      'transformations:any' can be used to specify that any value will be
   *      accepted, and by naming a type parameter, an input type will be
   *      propagated to the same type/name combination in the output schema
   *      (e.g. 'transformations:list<transformations:any Item>' might become
   *      'transformations:list<php:type:string>').
   *      The operation needs to ensure that the value actually conforms to
   *      this data type.
   *
   * If the operation doesn't define a value for the given @p $propertyKey,
   * NULL is returned.
   */
  abstract protected function outputInfo($inputKey, $propertyKey);


  /**
   * Specify the exact data type of the input data at input key @p $inputKey.
   * This data type is expected to conform to the one given by
   * @code inputProperty($inputKey, 'expectedType'); @endcode
   * in other words, it should not be less exact than the expected one.
   *
   * @param $inputKey
   *   The string key of the input slot whose data type is specified.
   * @param $type
   *   A type string like the one returned by TfDataWrapper::type() to specify
   *   the exact data type, or NULL to reset the data type to the one expected
   *   by the operation.
   *
   * @return
   *   TRUE if the input data type could be assigned, or FALSE if it didn't
   *   match the expected input data type for the given key.
   */
  public function setInputType($inputKey, $type) {
    $this->assertInputKeyExpected($inputKey);

    if (isset($type)) {
      $this->inputInfo[$inputKey]['assignedType'] = $type;
      $this->inputTypeChanged($inputKey);
    }
    elseif (isset($this->inputInfo[$inputKey]['assignedType'])) { // but not isset($type)
      unset($this->inputInfo[$inputKey]['assignedType']);
      $this->inputTypeChanged($inputKey);
    }
    // else {
    //   $this->inputInfo[$inputKey]['assignedType'] is unset before and
    //   after this function has been called, so nothing changes.
    // }
    return TRUE;
  }

  /**
   * Called when an input's data type has been changed/refined using
   * setInputType(). Subclasses can override this function to avoid the
   * expected output schema to be reset (which is done by default).
   */
  protected function inputTypeChanged($inputKey) {
    /// TODO: take away control from subclasses, and adapt the schema by ourselves?
    $this->updateOutputSchema($this->outputKeys());
  }

  /**
   * Specify that the expected input schema has been changed, as a reaction to
   * input type or value changes. Calling this function will update the set of
   * input keys from the inputs() method, and clear any input property caches
   * or assigned values that might be left for disappearing keys.
   *
   * If @p $changedKeys is provided, inputs will not only be cleared for
   * disappearing keys but in addition also for these given keys.
   * (The setInput() method will not be called - ensuring correctness of
   * internal operation state is the subclass's responsibility.)
   *
   * When all actions have been performed, registered TfOperationSlotListener
   * objects will be called with observedInputSchemaChanged().
   */
  protected function updateInputSchema($changedKeys = array()) {
    if (!is_array($changedKeys)) { // allow passing simple strings too
      $changedKeys = array($changedKeys);
    }
    $oldInput = $this->input;
    $oldInputInfo = $this->inputInfo;
    $oldKeys = array_keys($oldInputInfo);

    // Reset output keys and properties.
    $this->inputInfo = array_fill_keys($this->inputs(), array());
    $newKeys = array_keys($this->inputInfo);
    $this->input = array();

    $keyChanges = new stdClass();
    $commonKeys = array_intersect($oldKeys, $newKeys);
    $keyChanges->updated = array_intersect($changedKeys, $commonKeys);
    $remainingKeys = array_diff($commonKeys, $keyChanges->updated);
    $keyChanges->added = array_diff($commonKeys, $newKeys);
    $keyChanges->removed = array_diff($commonKeys, $oldKeys);

    foreach ($remainingKeys as $key) {
      if (isset($oldInput[$key])) {
        $this->input[$key] = $oldInput[$key];
      }
      $this->inputInfo[$key] = $oldInputInfo[$key];
    }
    foreach ($this->slotListeners as $listener) {
      $listener->observedInputSchemaChanged($this, $keyChanges);
    }
  }

  /**
   * Specify that the expected output schema has been changed, as a reaction to
   * input type or value changes. Calling this function will update the set of
   * output keys from the outputs() method, and clear any output property
   * caches that might be left for disappearing keys.
   *
   * If @p $changedKeys is provided, output properties will not only be cleared
   * for disappearing keys but in addition also for these given outputs.
   *
   * When all actions have been performed, registered TfOperationSlotListener
   * objects will be called with observedOutputSchemaChanged().
   */
  protected function updateOutputSchema($changedKeys = array()) {
    if (!is_array($changedKeys)) { // allow passing simple strings too
      $changedKeys = array($changedKeys);
    }
    $oldOutputInfo = $this->outputInfo;
    $oldKeys = array_keys($oldOutputInfo);

    // Reset output keys and properties.
    $this->outputInfo = array_fill_keys($this->outputs(), array());
    $newKeys = array_keys($this->outputInfo);

    $keyChanges = new stdClass();
    $commonKeys = array_intersect($oldKeys, $newKeys);
    $keyChanges->updated = array_intersect($changedKeys, $commonKeys);
    $remainingKeys = array_diff($commonKeys, $keyChanges->updated);
    $keyChanges->added = array_diff($commonKeys, $newKeys);
    $keyChanges->removed = array_diff($commonKeys, $oldKeys);

    foreach ($remainingKeys as $key) {
      $this->outputInfo[$key] = $oldOutputInfo[$key];
    }
    foreach ($this->slotListeners as $listener) {
      $listener->observedOutputSchemaChanged($this, $keyChanges);
    }
  }

  /**
   * Register a TfOperationSlotListener object with this operation, so that it
   * will be notified when this operation's input or output schemas or
   * input values change.
   */
  final public function registerSlotListener(TfOperationSlotListener $listener) {
    $this->slotListeners->attach($listener);
  }

  /**
   * Unregister a TfOperationSlotListener object from this operation.
   */
  final public function unregisterSlotListener(TfOperationSlotListener $listener) {
    $this->slotListeners->detach($listener);
  }

  /** @} End of "defgroup connection-info". */


  /**
   * @defgroup input-data Input data
   * @{
   * Methods that deal with the actual input data of the operation, including
   * functionality to assign and retrieve that data.
   */

  /**
   * Assign a piece of data to this operation's input at @p $inputKey.
   *
   * If the @p $input parameter is NULL, the data for @p $inputKey will be
   * unset (and default values will be used, if any exist for the given key).
   *
   * If you pass an @p $input that is not a TfDataWrapper object, it will be
   * automatically wrapped into a TfDataWrapper. If @p $input does not conform
   * to the expected type, you're in big trouble - make sure to never let this
   * happen.
   */
  public function setInput($inputKey, $input) {
    $this->assertInputKeyExpected($inputKey);
    $previousValue = $this->input($inputKey);

    if (isset($input)) {
      if (!is_object($input) || !($input instanceof TfDataWrapper)) {
        $input = TfDataWrapper::create($input);
      }
      $this->input[$inputKey] = $input;
      $this->inputChanged($inputKey, $previousValue);
    }
    elseif (isset($this->input[$inputKey])) { // but not isset($input)
      unset($this->input[$inputKey]);
      $this->inputChanged($inputKey, $previousValue);
    }
    // else {
    //   $this->input is NULL before and after this function, no changes.
    // }
  }

  /**
   * Retrieve a piece of input data that was previously assigned to this
   * operation. Each piece of input data is represented as a TfDataWrapper.
   * If no associated input data exists, the default value for this input is
   * returned, or NULL if no default value is defined by the operation.
   */
  public function input($inputKey) {
    return $this->isInputSet($inputKey)
      ? $this->input[$inputKey]
      : $this->inputProperty($inputKey, 'defaultValue');
  }

  /**
   * Called when input data has been (re-)set using setInput(). Subclasses can
   * override this function to avoid the output cache to be reset (which is
   * done by default).
   */
  protected function inputChanged($inputKey, $previousValue) {
    $this->clearOutputCache();
  }

  /**
   * Specify that the operation needs to be (re-)executed.
   * Preferably called when input data changes.
   */
  protected function clearOutputCache() {
    $this->outputCache = NULL;
  }

  /**
   * Return TRUE if an input has been set via setInput(), or FALSE if it is
   * unset (even if there is a default value for that key).
   */
  public function isInputSet($inputKey) {
    return isset($this->input[$inputKey]);
  }

  /**
   * Return TRUE if all required input values have been set, or FALSE otherwise.
   */
  public function allRequiredInputsSet() {
    foreach ($this->inputKeys() as $inputKey) {
      if (!$this->isInputSet($inputKey) && $this->inputProperty($inputKey, 'required')) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /** @} End of "defgroup input-data". */


  /**
   * @defgroup execution Operation execution
   * @{
   * Methods for actually executing the operation and influencing various
   * aspects of the execution process.
   */

  /**
   * In order to guarantee correct execution - such as, not being skipped for
   * performance reasons - operations with side effects need to override this
   * function and return TRUE instead. An operation is said to have side
   * effects if it takes any actions other than simply constructing the output
   * data, for example by writing data to a file or to the database.
   */
  public function hasSideEffects() {
    return FALSE;
  }

  /**
   * Override with "return TRUE;" to enable output caching.
   */
  protected function isOutputCachingEnabled() {
    return FALSE;
  }

  /**
   * Return the result of the operation as TfDataWrapper, given its current set
   * of input values. If the operation was unsuccessful, the isValid() method
   * of the resulting TfOutput object will return FALSE, otherwise you can
   * fetch the results via its at() method.
   */
  public function output() {
    $output = new TfOutput($this);

    if (!$this->allRequiredInputsSet()) {
      $missing_inputs = array();
      foreach ($this->inputKeys() as $key) {
        if (!$this->isInputSet($key) && $this->inputProperty($key, 'required')) {
          $missing_inputs[] = '"' . $this->inputProperty($key, 'label') . '"';
        }
      }
      $output->setErrorMessage(t('Not all required inputs are set for operation "!opname", missing: !missing-inputs.', array('!missing-inputs' => implode(', ', $missing_inputs))));
      return $output;
    }
    if ($this->isOutputCachingEnabled()) {
      if (isset($this->outputCache)) {
        return $this->outputCache;
      }
    }
    $this->execute($output);
    $output->operationDone();

    if ($this->isOutputCachingEnabled()) {
      $this->outputCache = $output;
    }
    return $output;
  }

  /**
   * Calculate the result of the operation (without concerns to caching) given
   * its current input values, and call $output->set() for each output value.
   * Call $output->setErrorMessage() if any errors occurred.
   */
  abstract protected function execute(TfOutput $output);

  /** @} End of "defgroup execution". */


  //
  // Private functions that are only of interest to TfOperation itself.
  //

  /**
   * Make sure an input with the given @p $inputKey is among the possible input
   * slots, and throw an exception if that is not the case.
   */
  private function assertInputKeyExpected($inputKey) {
    if (!isset($this->inputInfo[$inputKey])) {
      throw new Exception('No input key "' . $inputKey .
        '" possible for operation ' . $this->uiName());
    }
  }

  /**
   * Make sure an output with the given @p $outputKey is among the possible
   * output slots, and throw an exception if that is not the case.
   */
  private function assertOutputKeyExpected($outputKey) {
    if (!isset($this->outputInfo[$outputKey])) {
      throw new Exception('No output key "' . $outputKey .
        '" expected for operation ' . $this->uiName());
    }
  }
}
