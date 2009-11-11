<?php
// $Id: transformations.pipeline.inc,v 1.13 2009/06/23 20:13:58 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * This file contains code related to the management of operation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * A pipeline is essentially a blueprint of data flows, with data being passed
 * from one operation to the next. Any output of an operation can be used as
 * input for multiple other operations, whereas an operation's input comes from
 * exactly one source. The pipeline itself can also have inputs and outputs,
 * both of these are correlated to exactly one operation input/output.
 *
 * The TfPipeline class handles the management and storage of pipelines.
 * In order to be executed, it needs to be fed to a TfPipelineOperation that
 * makes use of the pipeline specification in the TfPipeline and executes it.
 */
class TfPipeline implements Serializable {
  // Constants denoting data source / data target types.
  const Data      = 1;
  const Parameter = 2;
  const Operation = 3;
  const Output    = 4;

  // A constant denoting a "don't care" name for pipeline parameters or outputs,
  // TfPipeline will generate a name that seems appropriate.
  const NewOutboundConnection = -1;

  /** The pipeline identifier: an integer, or NULL if the pipeline
   * is not (yet) stored in the database. */
  private $id;

  /** The currently assigned name of the pipeline. */
  private $name;

  /** An array of (serializable) pipeline properties. */
  private $properties;

  /** The set of operations used by this pipeline.
   * The operation identifier string is used as key, and the value is an array
   * including the operation's class name in the 'name' element as well as
   * additional (serializable) custom properties that can be stored using other
   * array keys. See the operationProperty() and setOperationProperty() methods
   * for more information on that. */
  private $operations;

  /** A set of input sources for the target operations / pipeline outputs in
   * this array. See the sources() method for the array format. */
  private $sources;

  /**
   * A set of output targets for the source operations / pipeline parameters in
   * this array. This can be completely reconstructed from the $sources array
   * and is only stored for easier / more efficient read access. (On the other
   * hand, $sources can not be reconstructed from $targets because direct data
   * sources are not stored in here.)
   * See the targets() method for the array format.
   */
  private $targets;

  /** A boolean value indicating whether the pipeline has been modified
   * since it's been loaded from the database. */
  private $isModified;

  /** A cache of a pipeline's operation execution path. */
  private $executedOperationIds;

  /** A cache for TfPipeline objects, keyed by the pipeline id. */
  private static $pipelineCache = array();


  //
  // Static methods.
  //

  /**
   * For a given data source (as returned by the source() method), determine
   * which type of source it is, i.e. where to get the data from.
   *
   * @return
   *   TfPipeline::Parameter for a pipeline parameter, TfPipeline::Operation
   *   for the result value of another operation in the pipeline, or
   *   TfPipeline::Data if the source is a direct TfDataWrapper instance.
   *   If $source is not a valid data source specification, FALSE is returned
   *   (which is different than all the mentioned constants).
   */
  public static function sourceType($source) {
    return (!is_numeric($source['entity'])) ? self::Operation : $source['entity'];
  }

  /**
   * Check if a pipeline with the given @p $name already exists in the database.
   * If @p $excludeId is set to a pipeline id, the corresponding pipeline will
   * be ignored in the check (which means this function will check if any other
   * pipeline except the @p $excludeId one has got the same name.)
   *
   * @return
   *   TRUE if a pipeline with that name exists, or FALSE if not.
   */
  public static function existsName($name, $excludeId = FALSE) {
    $result = db_result(db_query(
      "SELECT name FROM {transformations_pipeline} WHERE name = '%s'"
      . (empty($excludeId) ? '' : ' AND pipeline_id <> %d'), $name, $excludeId
    ));
    return !empty($result);
  }

  /**
   * Load a pipeline with the given name from the database.
   *
   * This is a static factory method that implements internal caching for
   * pipeline objects. Note that because of this caching, it is possible to get
   * a pipeline returned that has a different name than was specified in the
   * (name) argument - this happens when a pipeline is loaded and afterwards
   * renamed with a call to the setName() method without save() being called.
   * In that case, calling load() with the old name (as it exists in the
   * database) will yield the renamed pipeline object.
   *
   * @param $arg
   *  The name of the pipeline or its integer identifier (pipeline_id).
   * @return
   *   A pipeline object or FALSE if it was not available.
   */
  public static function load($pipelineId) {
    // We want a NULL value to return TRUE for this existence check, so we
    // can't use isset() or empty().
    if (!array_key_exists($pipelineId, self::$pipelineCache)) {
      $data = db_fetch_object(db_query(
        "SELECT pipeline_id, name, properties, operations, sources
          FROM {transformations_pipeline} WHERE pipeline_id = %d", $pipelineId
      ));
      self::$pipelineCache[$pipelineId] = empty($data) ? NULL : $data;
    }

    if (!isset(self::$pipelineCache[$pipelineId])) {
      return FALSE;
    }
    $data = self::$pipelineCache[$pipelineId];

    $pipeline = new TfPipeline($data->name);
    $pipeline->id = (int) $data->pipeline_id;
    $pipeline->properties = unserialize($data->properties);
    $pipeline->operations = unserialize($data->operations);
    $pipeline->importSources(unserialize($data->sources));
    $pipeline->isModified = FALSE;
    $pipeline->regenerateTargets();

    return $pipeline;
  }

  /**
   * Reconstruct a pipeline object from the array that was returned by the
   * export() function. If the pipeline could not be reconstructed, FALSE is
   * returned instead of the pipeline object.
   */
  public static function import($exportedPipeline) {
    $pipeline = new TfPipeline($exportedPipeline['name']);
    $pipeline->properties = $exportedPipeline['properties'];
    $pipeline->operations = $exportedPipeline['operations'];
    $pipeline->importSources($exportedPipeline['sources']);
    $pipeline->isModified = TRUE;
    $pipeline->regenerateTargets();

    $pipe = array();
    foreach ($pipeline as $key => $value) {
      $pipe[$key] = $value;
    }
    return $pipeline;
  }


  //
  // Object (instance) methods.
  //

  /**
   * Create a pipeline with the given @p $name.
   */
  public function __construct($name) {
    $this->id = NULL;
    $this->name = $name;
    $this->properties = array();
    $this->operations = array();
    $this->sources = array();
    $this->targets = array();
    $this->isModified = TRUE;
    $this->executedOperationIds = NULL;
  }

  /**
   * Implementation of Serializable::serialize():
   * Return an string representation of this pipeline.
   */
  public function serialize() {
    return serialize(array(
      'id' => $this->id,
      'name' => $this->name,
      'properties' => $this->properties,
      'operations' => $this->operations,
      'sources' => $this->exportSources(),
      'isModified' => $this->isModified,
    ));
  }

  /**
   * Implementation of Serializable::unserialize():
   * Load pipeline state from the string representation returned by serialize().
   */
  public function unserialize($serialized) {
    $data = unserialize($serialized);
    $this->id = $data['id'];
    $this->name = $data['name'];
    $this->properties = $data['properties'];
    $this->operations = $data['operations'];
    $this->sources = $data['sources'];
    $this->isModified = $data['isModified'];

    $this->executedOperationIds = NULL;
    $this->importSources($this->sources);
    $this->regenerateTargets();
  }

  /**
   * Create an array that can be run through serialize() and unserialize()
   * and afterwards can be passed to TfPipeline::import() in order to recreate
   * a pipeline object with exactly similar configuration of operations and
   * connections.
   *
   * Note that this function does not export the pipeline id that's being used
   * for unique identification in the database, so if an exported pipeline is
   * imported and then saved, it will not overwrite the original one but create
   * a new pipeline entry in the database. Not exporting pipeline ids makes it
   * possible to interchange pipeline configurations across different sites.
   */
  public function export() {
    return array(
      'name' => $this->name,
      'properties' => $this->properties,
      'operations' => $this->operations,
      'sources' => $this->exportSources(),
    );
  }

  /**
   * Return a version of the $this->sources array that can be serialized.
   */
  private function exportSources() {
    $exportedSources = array();

    foreach ($this->sources as $targetEntity => $sources) {
      foreach ($sources as $targetKey => $source) {
        if ($source['entity'] == TfPipeline::Data) {
          // Legacy data conversion, should be removed soon.
          if (!is_object($source['data']) || !($source['data'] instanceof TfDataWrapper)) {
            $source['data'] = TfDataWrapper::create($source['data']);
          }
          $exportedSources[$targetEntity][$targetKey] = array(
            'entity' => TfPipeline::Data,
            'data' => $source['data']->export(),
          );
        }
        else {
          $exportedSources[$targetEntity][$targetKey] = $source;
        }
      }
    }
    return $exportedSources;
  }

  private function importSources($sources) {
    $importedSources = array();

    foreach ($sources as $targetEntity => $sources) {
      foreach ($sources as $targetKey => $source) {
        if (isset($source['data'])) {
          $importedSources[$targetEntity][$targetKey] = array(
            'entity' => TfPipeline::Data,
            'data' => TfDataWrapper::import($source['data']),
          );
        }
        else {
          $importedSources[$targetEntity][$targetKey] = $source;
        }
      }
    }
    $this->sources = $importedSources;
  }

  /**
   * Return TRUE if the pipeline has been modified compared to when it has been
   * loaded from the database, or if it has not yet been saved at all.
   * Return FALSE if the pipeline was loaded from the database but has not been
   * modified since then.
   */
  public function isModified() {
    return $this->isModified;
  }


  /**
   * Save this pipeline to the database. After saving, the id() method will
   * return a valid integer id for this pipeline.
   *
   * @return
   *   TRUE if saving was successful, or FALSE if the pipeline could not be
   *   written to the database for any reason.
   */
  public function save() {
    $data = new stdClass();
    $data->pipeline_id = $this->id;
    $data->name = $this->name;
    $data->properties = serialize($this->properties);
    $data->operations = serialize($this->operationSpecifications());
    $data->sources = serialize($this->exportSources());

    // Write the pipeline to the database, and update internal data.
    $update = isset($this->id) ? array('pipeline_id') : array();
    $success = drupal_write_record('transformations_pipeline', $data, $update);

    if (!$success) {
      return FALSE;
    }

    $this->id = $data->pipeline_id;
    $this->isModified = FALSE;
    self::$pipelineCache[$this->id]   = $this;
    return TRUE;
  }

  /**
   * Delete this pipeline from the database. The pipeline object itself, with
   * all its properties, stays fully functional and can still be manipulated,
   * executed or stored again.
   *
   * If the pipeline is not (yet) stored in the database, no actions will
   * be taken.
   */
  public function deleteFromDatabase() {
    if (!isset($this->id)) {
      return;
    }
    db_query("DELETE FROM {transformations_pipeline}
              WHERE pipeline_id = %d", $this->id);

    unset(self::$pipelineCache[$this->id]);
    $this->id = NULL;
    $this->isModified = TRUE;
  }


  /**
   * Return the id of this pipeline, or FALSE if the pipeline does not exist
   * in the database.
   *
   * @see TfPipeline::save()
   */
  public function id() {
    return isset($this->id) ? $this->id : FALSE;
  }

  /**
   * Return the name of this pipeline.
   *
   * @see TfPipeline::setName()
   */
  public function name() {
    return $this->name;
  }

  /**
   * Set a new name for this pipeline. (Any string is allowed as name.)
   *
   * @see TfPipeline::name()
   */
  public function setName($newName) {
    if ($this->name != $newName) {
      $this->isModified = TRUE;
    }
    $this->name = $newName;
  }

  /**
   * Assign a custom property to an operation.
   *
   * The property key may be any string. TfPipeline further defines the
   * property key 'description' which is expected to contain a string value,
   * any other property keys are up to custom usage and can contain any
   * (serializable) value. Those keys should probably be prefixed with the
   * respective module name, in order to prevent clashes.
   *
   * @param $propertyKey
   *   The string that specifies which property is being written.
   * @param $value
   *   The (serializable) value that you want to assign to the pipeline,
   *   or NULL to unset the property.
   */
  public function setProperty($propertyKey, $value) {
    if (is_null($value)) {
      unset($this->properties[$propertyKey]);
    }
    elseif ($propertyKey == 'description' && empty($value)) {
      unset($this->properties[$propertyKey]);
    }
    else {
      $this->properties[$propertyKey] = $value;
    }
    $this->isModified = TRUE;
  }

  /**
   * Retrieve a custom property of this pipeline that was previously assigned
   * with the setProperty() method.
   *
   * @param $propertyKey
   *   The string that specifies which property is being read.
   *
   * @return
   *   The (serializable) value that was assigned with setProperty(), or NULL
   *   if no property is set for the given property key. For the predefined
   *   property key 'description', a default string value will be returned
   *   even if the property has not yet been assigned a custom value.
   */
  public function property($propertyKey) {
    if (isset($this->properties[$propertyKey])) {
      return $this->properties[$propertyKey];
    }
    elseif ($propertyKey == 'description') {
      return '';
    }
    return NULL;
  }

  /**
   * Add an operation to the pipeline.
   *
   * @param $operation
   *   The class name of the operation, or an already instantiated
   *   TfOperation object.
   *
   * @return
   *   A unique string identifier for this operation ($operationId) in the
   *   context of this pipeline. (You can add the same type of operation
   *   multiple times, and each one will receive a different identifier.)
   *   This identifier is used in other methods of this class to specify this
   *   operation, so you might want to keep it at hand if you intend to set up
   *   the pipeline.
   */
  public function addOperation($operation) {
    $isObject = (is_object($operation) && $operation instanceof TfOperation);
    $className = $isObject ? get_class($operation) : $operation;

    // Generate an operation identifier, e.g. "TfArrayFromStdObject-1"
    $operationId = self::uniqueIdentifier($className, $this->operations, TRUE, '-');
    $this->operations[$operationId] = array();

    if ($isObject) {
      $this->operations[$operationId]['name'] = get_class($operation);
    }
    else {
      $this->operations[$operationId]['name'] = $className;
    }

    $this->isModified = TRUE;
    return $operationId;
  }

  /**
   * Generate a unique identifier that is not a key for @p $keyedArray.
   * The identifier will start with @p $baseName.
   */
  private static function uniqueIdentifier($baseName, $keyedArray, $alwaysAppendSuffix = FALSE, $spacer = '-') {
    if (!isset($keyedArray[$baseName]) && !$alwaysAppendSuffix) {
      // $alwaysAppendSuffix == FALSE: no suffix for the first element
      return $baseName;
    }
    // $alwaysAppendSuffix == TRUE: ($spacer . '1') suffix for the first element
    $i = $alwaysAppendSuffix ? 1 : 2;
    while (isset($keyedArray[$baseName . $spacer . $i])) {
      ++$i;
    }
    return $baseName . $spacer . $i;
  }

  /**
   * Assign a custom property to an operation.
   *
   * The property key may be any string except for 'name', which is reserved
   * for internal usage. TfPipeline further defines the property keys 'label'
   * and 'description' which are expected to contain string values, any other
   * property keys are up to custom usage and can contain any (serializable)
   * value. Those keys should probably be prefixed with the respective module
   * name, in order to prevent clashes.
   *
   * @param $operationId
   *   The identifier of the operation that should be assigned the property.
   * @param $propertyKey
   *   The string that specifies which property is being written.
   * @param $value
   *   The (serializable) value that you want to assign to the operation,
   *   or NULL to unset the property.
   *
   * @return
   *   TRUE if the property was assigned, or FALSE if that was not possible.
   *   Assigning the property will fail when no operation with @p $operationId
   *   as operation identifier exists in this pipeline, or when you try to set
   *   the value for the reserved property key 'name'.
   */
  public function setOperationProperty($operationId, $propertyKey, $value) {
    if (!isset($this->operations[$operationId])) {
      return FALSE;
    }
    elseif ($propertyKey == 'name') {
      return FALSE;
    }
    elseif (is_null($value)) {
      unset($this->operations[$operationId][$propertyKey]);
    }
    elseif (in_array($propertyKey, array('label', 'description')) && empty($value)) {
      unset($this->operations[$operationId][$propertyKey]);
    }
    elseif ($propertyKey == 'label' && $value == $operationId) {
      // That's the default value anyways, don't unnecessarily store it.
      unset($this->operations[$operationId][$propertyKey]);
    }
    else {
      $this->operations[$operationId][$propertyKey] = $value;
    }

    $this->isModified = TRUE;
    return TRUE;
  }

  /**
   * Retrieve a custom property of an operation that was previously assigned
   * with the setOperationProperty() method.
   *
   * @param $operationId
   *   The identifier of the operation that has been assigned the property.
   * @param $propertyKey
   *   The string that specifies which property is being read.
   *
   * @return
   *   The (serializable) value that was assigned with setOperationProperty(),
   *   or NULL if no property is set for the given property key. For the
   *   predefined property keys 'label' and 'description', a default string
   *   value will be returned even if the property has not yet been assigned
   *   a custom value.
   */
  public function operationProperty($operationId, $propertyKey) {
    if (!isset($this->operations[$operationId])) {
      return NULL;
    }
    elseif ($propertyKey == 'name') {
      return NULL;
    }
    elseif (isset($this->operations[$operationId][$propertyKey])) {
      return $this->operations[$operationId][$propertyKey];
    }
    elseif ($propertyKey == 'label') {
      return $operationId;
    }
    elseif ($propertyKey == 'description') {
      $op_info = transformations_operation_info($this->operations[$operationId]['name']);
      return empty($op_info['description']) ? '' : $op_info['description'];
    }
    return NULL;
  }

  /**
   * Remove an operation and all of the mappings that are connected to its
   * inputs and outputs. If no such operation exists in this pipeline, no
   * actions will be taken.
   *
   * @param $operationId
   *   The identifier of the operation that should be removed from the pipeline.
   */
  public function removeOperation($operationId) {
    // Remove the input and output mappings from other operations.
    foreach ($this->operations as $otherOperationId => $operation) {
      if ($otherOperationId == $operationId) {
        continue;
      }
      foreach ($this->sources($operationId) as $inputKey => $mapping) {
        $this->disconnectTarget($operationId, $inputKey);
      }
      foreach ($this->targets($operationId) as $outputKey => $mapping) {
        $this->disconnectSource($operationId, $outputKey);
      }
    }
    unset($this->operations[$operationId]);
    $this->isModified = TRUE;
    $this->executedOperationIds = NULL;
  }

  /**
   * Return an array of operation identifiers within this pipeline.
   */
  public function operationIds() {
    return array_keys($this->operations);
  }

  /**
   * Return an array of operation specifications with the corresponding
   * operation identifiers as array keys, where a single specification
   * is a structured array with the property 'name' referring to the class name
   * of that operation. Additional custom properties (that have been previously
   * set using the setOperationProperties() method) are also included as array
   * elements.
   */
  public function operationSpecifications() {
    return $this->operations;
  }

  /**
   * Return an array of newly instantiated TfOperation objects with the
   * corresponding operation identifiers as array keys.
   *
   * @param $invalidOperations
   *   If any operations could not be instantiated, this array will contain
   *   their operation identifiers after returning from this method.
   */
  public function createOperations(&$invalidOperations = array()) {
    $instances = array();

    foreach ($this->operations as $operationId => $operation) {
      try {
        $instances[$operationId] = $this->createOperation($operationId);
      }
      catch (Exception $e) {
        $invalidOperations[] = $operationId;
      }
    }
    return $instances;
  }

  /**
   * Return a newly instantiated TfOperation object for the given
   * operation identifier string.
   *
   * An exception is thrown when the given operation id does not correspond to
   * an operation in the pipeline.
   */
  public function createOperation($operationId) {
    $operation = TfOperation::load($this->operations[$operationId]['name']);

    if (!$operation) {
      throw new Exception('Could not instantiate operation ' . $operationId .
        ' (from "' . print_r($this->operations[$operationId], TRUE) .
        '") in pipeline ' . $this->name() . '.');
    }
    $this->assignOperationFixedValues($operationId, $operation);
    return $operation;
  }

  /**
   * Assign all fixed input data to a given operation, which might also affect
   * its input and output schemas as well as change input values.
   */
  private function assignOperationFixedValues($operationId, TfOperation $operation) {
    $assignedKeys = array();
    $sources = $this->sources($operationId);
    $observer = new TfPipelineOperationInputSchemaObserver();
    $operation->registerSlotListener($observer);
    $observer->inputSchemaChanged = TRUE;

    // (Try to) Assign all parameters in their correct order, which is the one
    // that the operation returns with its inputKeys() function.
    while ($observer->inputSchemaChanged) {
      $observer->inputSchemaChanged = FALSE;

      foreach ($operation->inputKeys() as $inputKey) {
        if (!isset($sources[$inputKey]) || isset($assignedKeys[$inputKey])) {
          continue;
        }
        $assignedKeys[$inputKey] = TRUE;
        $source = $sources[$inputKey];

        if (isset($source['data'])) {
          $operation->setInput($inputKey, $source['data']);
        }
        if ($observer->inputSchemaChanged) {
          break; // Try again with the new input keys.
        }
      }
    }

    $operation->unregisterSlotListener($observer);
    return $operation;
  }


  /**
   * Map the a "source" of data (either an operation output, a pipeline
   * parameter, or a TfDataWrapper object) to a data "target" (either an
   * operation input or a pipeline output).
   *
   * The previous source of the given target, if any existed before, is
   * discarded and replaced by the new one. Also, a given pipeline parameter
   * can never be connected to more than one operation input, so any previous
   * connection to a parameter of the same name will be replaced as well.
   *
   * If the source is a pipeline parameter that did not yet exist, it will be
   * created by this method.
   *
   * @param $sourceEntity
   *   Where to get the data from. This is either the operation identifier of
   *   the source operation, or TfPipeline::Parameter for a parameter source,
   *   or TfPipeline::Data if you directly pass a TfDataWrapper as $sourceData.
   * @param $sourceData
   *   Depending on the type of $sourceEntity, this describes either the
   *   output key of the source operation, the parameter key, or an actual
   *   TfDataWrapper object.
   * @param $targetEntity
   *   Where to put the data. This is either the operation identifier of the
   *   target operation, or TfPipeline::Output for a pipeline output.
   *   Pipeline outputs can only be connected to operation outputs, so
   *   specifying a @p $sourceEntity TfPipeline::Parameter or TfPipeline::Data
   *   for a @p $targetEntity TfPipeline::Output will throw an exception.
   * @param $targetData
   *   Depending on the type of $targetEntity, this describes either the
   *   input key of the target operation or the key of the pipeline output.
   */
  public function connect($sourceEntity, $sourceData, $targetEntity, $targetData) {
    if (!is_numeric($sourceEntity)) {
      $this->assertOperationExists($sourceEntity);
    }
    if (!is_numeric($targetEntity)) {
      $this->assertOperationExists($targetEntity);
    }
    if ($sourceEntity == $targetEntity) {
      throw new Exception('Creating circular data flows is not allowed!');
    }
    if ($sourceEntity == TfPipeline::Parameter && $targetEntity == TfPipeline::Output) {
      throw new Exception('Connecting pipeline parameters to pipeline outputs is not allowed!');
    }
    if ($sourceEntity == TfPipeline::Parameter && $sourceData != TfPipeline::NewOutboundConnection) {
      // A parameter may only be connected to a single operation input -
      // otherwise, we run into troubles with determining is expected schema.
      // So if any connections to that parameter already exist, remove them
      // prior to adding the newly inserted one.
      $this->disconnectSource($sourceEntity, $sourceData);
    }
    $this->disconnectTarget($targetEntity, $targetData);

    // If no specific keys are given for a parameter or output,
    // assign the key name of the connected operation's output or input
    // (e.g. Parameter "filepath" if no such parameter exists yet,
    // or "filepath-2" if a "filepath" parameter already existed before.)
    if ($sourceEntity == TfPipeline::Parameter && $sourceData == TfPipeline::NewOutboundConnection) {
      $sourceData = self::uniqueIdentifier($targetData, $this->targets(TfPipeline::Parameter));
    }
    if ($targetEntity == TfPipeline::Output && $targetData == TfPipeline::NewOutboundConnection) {
      $targetData = self::uniqueIdentifier($sourceData, $this->sources(TfPipeline::Output));
    }

    if ($sourceEntity == TfPipeline::Data) {
      if (isset($sourceData)) {
        if (!is_object($sourceData) || !($sourceData instanceof TfDataWrapper)) {
          $sourceData = TfDataWrapper::create($sourceData);
        }
        $this->sources[$targetEntity][$targetData] = array(
          'entity' => TfPipeline::Data,
          'data' => $sourceData,
        );
      }
      else {
        $this->disconnectTarget($targetEntity, $targetData);
      }
    }
    else {
      $this->sources[$targetEntity][$targetData] = array(
        'entity' => $sourceEntity,
        'key' => $sourceData,
      );
      $this->targets[$sourceEntity][$sourceData][$targetEntity][$targetData] = TRUE;
    }
    $this->isModified = TRUE;
    $this->executedOperationIds = NULL;
  }

  /**
   * Remove the connection to the given data target.
   * If no connection to that target exists, no actions will be taken.
   */
  public function disconnectTarget($targetEntity, $targetData) {
    if (!isset($this->sources[$targetEntity][$targetData])) {
      return;
    }

    $source = $this->sources[$targetEntity][$targetData];

    // Remove the target connection from the data source, except if the
    // data source is a TfDataWrapper object (in which case there is no
    // connection from this object to the data target).
    if ($source['entity'] != TfPipeline::Data) {
      $sourceEntity = $source['entity'];
      $sourceData   = $source['key'];

      unset($this->targets[$sourceEntity][$sourceData][$targetEntity][$targetData]);

      if (empty($this->targets[$sourceEntity][$sourceData][$targetEntity])) {
        unset($this->targets[$sourceEntity][$sourceData][$targetEntity]);
      }
      if (empty($this->targets[$sourceEntity][$sourceData])) {
        unset($this->targets[$sourceEntity][$sourceData]);
      }
      if (empty($this->targets[$sourceEntity])) {
        unset($this->targets[$sourceEntity]);
      }
    }

    // Remove the source connection from the data target.
    unset($this->sources[$targetEntity][$targetData]);

    if (empty($this->sources[$targetEntity])) {
      unset($this->sources[$targetEntity]);
    }
    $this->isModified = TRUE;
    $this->executedOperationIds = NULL;
  }

  /**
   * Remove the connection from the given data source.
   * If no connections from that source exist, no actions will be taken.
   *
   * $sourceEntity must not be TfPipeline::Data, because a TfDataWrapper object
   * does not know its outgoing connections and therefore can only be
   * disconnected with a call to disconnectTarget().)
   */
  public function disconnectSource($sourceEntity, $sourceData) {
    $targets = $this->targets($sourceEntity, $sourceData);

    // disconnectTarget() does it all for us, let's just reuse it.
    foreach ($targets as $targetEntity => $targetDataSpecs) {
      foreach ($targetDataSpecs as $targetData => $true) {
        $this->disconnectTarget($targetEntity, $targetData);
      }
    }
    // disconnectTarget() already sets the isModified property.
  }

  /**
   * Returns an array with sets of data sources for each data target that has
   * been connected to at least one source. The format goes like this:
   *
   * <code>
   * // $targetEntity is either of ($operationId | TfPipeline::Output)
   * // $targetData is the desired input key for the target operation,
   * //          or the pipeline result key that should be assigned the value
   * $sources[$targetEntity][$targetData] = array(
   *   'entity' => $sourceEntity, // either of ($operationId | TfPipeline::Parameter | TfPipeline::Data)
   *   'key'    => $outputKey,      // for operation sources, or...
   *   'key'    => $parameterKey,   // for parameter sources, or...
   *   'data'   => $aTfDataWrapperObject, // for direct data sources
   * );
   * </code>
   *
   * @param $targetEntity
   *   If set, this method returns the contents of $sources[$targetEntity]
   *   instead of the whole $sources array, or an empty array if no such
   *   element exists.
   * @return
   *   The $sources array, or part of it if @p $targetEntity has been set.
   */
  public function sources($targetEntity = NULL) {
    if (isset($targetEntity)) {
      if (func_num_args() > 1) {
        throw new Exception('TfPipeline::sources() has been called with two or more arguments although it can only cope with one - most likely, you wanted to call TfPipeline::source() instead for retrieving a single connection source specification.');
      }
      return empty($this->sources[$targetEntity])
        ? array() : $this->sources[$targetEntity];
    }
    return $this->sources;
  }

  /**
   * Returns the data source for the given data target, or FALSE if the target
   * has not been connected to any source.
   *
   * @param $targetEntity
   *   Either the operation identifier of the target operation,
   *   or TfPipeline::Output for a pipeline output.
   * @param $targetData
   *   Depending on the type of $targetEntity, this describes either the
   *   input key of the target operation or the key of the pipeline output.
   */
  public function source($targetEntity, $targetData) {
    return empty($this->sources[$targetEntity][$targetData])
      ? FALSE : $this->sources[$targetEntity][$targetData];
  }

  /**
   * A set of output targets for the source operations / pipeline parameters in
   * this array. The format goes like this:
   *
   * <code>
   * // Same variables as in sources(), except that $sourceEntity may not be
   * // TfPipeline::Data for obvious reasons - direct data won't change, so it
   * // does not need a forward connection to its targets.
   * // For $sourceEntity is TfPipeline::Parameter, $targetEntity may only be
   * // an operation identifier but not TfPipeline::Output.
   * $targets[$sourceEntity][$sourceData][$targetEntity][$targetData] = TRUE;
   * </code>
   *
   * @param $sourceEntity
   *   If set, this method returns the contents of $targets[$sourceEntity]
   *   instead of the whole $targets array, or an empty array if no such
   *   element exists.
   * @param $sourceData
   *   If set, this method returns the contents of
   *   $targets[$sourceEntity][$sourceData] instead of the
   *   whole $targets array, or an empty array if no such element exists.
   * @return
   *   The $targets array, or part of it if parameters have been set.
   */
  public function targets($sourceEntity = NULL, $sourceData = NULL) {
    if (isset($sourceEntity)) {
      if (isset($sourceData)) {
        return empty($this->targets[$sourceEntity][$sourceData])
            ? array() : $this->targets[$sourceEntity][$sourceData];
      }
      return empty($this->targets[$sourceEntity])
          ? array() : $this->targets[$sourceEntity];
    }
    return $this->targets;
  }

  /**
   * Rebuild the $targets array, using only the $sources array as input.
   */
  private function regenerateTargets() {
    foreach ($this->sources as $targetEntity => $sourcesByTargetData) {
      foreach ($sourcesByTargetData as $targetData => $source) {
        if (!isset($source['key'])) { // TfPipeline::Data sources
          continue;
        }
        $this->targets[$source['entity']][$source['key']][$targetEntity][$targetData] = TRUE;
      }
    }
  }


  /**
   * Determine the execution order for the operations to run properly.
   */
  public function executionOrder() {
    if (isset($this->executedOperationIds)) {
      return $this->executedOperationIds;
    }
    $remainingOperationIds = array();

    // Step 1: Determine which operations need to run in any case.
    foreach ($this->targets(TfPipeline::Parameter) as $parameterKey => $targets) {
      foreach ($targets as $targetEntity => $targetDataSpecs) {
        if ($targetEntity != TfPipeline::Output) {
          $remainingOperationIds[$targetEntity] = TRUE;
        }
      }
    }
    foreach ($this->sources(TfPipeline::Output) as $outputKey => $source) {
      if ($source['entity'] != TfPipeline::Parameter) {
        $remainingOperationIds[$source['entity']] = TRUE;
      }
    }

    // Step 2: Pick all of those operations in any order, and make sure that no
    //   operation runs before all of its dependencies have been executed too.
    $pathOperationIds = array();
    $remainingOperationIds = empty($remainingOperationIds)
      ? array_keys($this->operations)
      : array_keys($remainingOperationIds);
    $forbiddenSourceOperationIds = array(); // a.k.a. cyclic dependency guard

    while (!empty($remainingOperationIds)) {
      $currentOperationId = array_shift($remainingOperationIds);
      $forbiddenSourceOperationIds[] = $currentOperationId;

      // Never run any operation twice. The whole algorithm depends on this.
      if (in_array($currentOperationId, $pathOperationIds)) {
        continue;
      }

      // Check if any sources need to run first, and if so, defer scheduling
      // this operation until those dependencies have been taken care of.
      $dependencies = array();

      foreach ($this->sources($currentOperationId) as $targetInputKey => $source) {
        if (!is_numeric($source['entity'])) {
          // Sources that have already run do not bother us.
          if (in_array($source['entity'], $pathOperationIds)) {
            continue;
          }
          // Here's a source that needs to be run first. Schedule it.
          self::unshiftOperationId($dependencies, $source['entity']);
        }
      }

      if (!empty($dependencies)) {
        while (!empty($dependencies)) {
          $sourceOperationId = array_pop($dependencies);
          self::unshiftOperationId($remainingOperationIds, $sourceOperationId);
        }
        continue;
      }
      $pathOperationIds[] = $currentOperationId;
      $targetOperationIds = array();

      // Now that the current operation has been taken care of, schedule the
      // operations that are connected to the current one's outputs.
      foreach ($this->targets($currentOperationId) as $sourceOutputKey => $targets) {
        foreach ($targets as $targetEntity => $targetDataSpecs) {
          if (!is_numeric($targetEntity)) {
            self::unshiftOperationId($targetOperationIds, $targetEntity);
          }
        }
      }
      while (!empty($targetOperationIds)) {
        $targetOperationId = array_shift($targetOperationIds);
        self::unshiftOperationId($remainingOperationIds, $targetOperationId);
      }
    }

    $this->executedOperationIds = $pathOperationIds;
    return $pathOperationIds;
  }

  private static function unshiftOperationId(&$operationIds, $operationId) {
    if (in_array($operationId, $operationIds)) {
      // No operation shall be executed twice, and this condition also helps
      // to avoid circular (infinite) additions.
      return;
    }
    array_unshift($operationIds, $operationId);
  }


  /**
   * Ensure that an operation with the given @p $operationId exists,
   * and throw an exception if it doesn't.
   */
  protected function assertOperationExists($operationId) {
    if (!isset($this->operations[$operationId])) {
      throw new Exception(
        'No operation in this pipeline with operation id ' . $operationId
      );
    }
  }
}

/**
 * An incredibly lightweight operation slot listener that sets its
 * inputSchemaChanged flag to TRUE when the observed operation's input schema
 * changes.
 */
class TfPipelineOperationInputSchemaObserver implements TfOperationSlotListener {
  public $inputSchemaChanged;

  public function __construct() {
    $this->inputSchemaChanged = FALSE;
  }

  public function observedInputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges) {
    $this->inputSchemaChanged = TRUE;
  }

  public function observedInputChanged(TfOperation $changedOperation, $inputKey) {}
  public function observedOutputSchemaChanged(TfOperation $changedOperation, stdClass $keyChanges) {}
}
