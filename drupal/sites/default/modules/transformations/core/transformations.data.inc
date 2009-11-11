<?php
// $Id: transformations.data.inc,v 1.5 2009/06/23 22:20:25 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

class TfDataType {
  /**
   * Return the PHP type of the given data as Transformations data type.
   */
  public static function phpType($data) {
    if (is_object($data)) {
      $type = 'php:class:' . get_class($data);
    }
    elseif (is_int($data) || is_float($data) || is_double($data)) {
      $type = 'php:type:number:' . gettype($data);
    }
    else {
      $type = 'php:type:' . gettype($data);
    }
    return $type;
  }

  /**
   * Determine whether the @p $checkedType is a subtype of @p $referenceType,
   * and thus always compatible to the latter.
   */
  public static function subtypeOf($checkedType, $referenceType) {
    ///TODO: Consider type parameters and inheritance in a proper way.

    // Some intermediary hacks to make simple stuff work.
    // In the end, this should all be done by pluggable hooks, stored in a
    // cache table and non-dynamic so that it can be reused by JavaScript.
    if ($referenceType == 'transformations:any') {
      return TRUE;
    }
    if ($referenceType == 'transformations:list' && $checkedType == 'php:type:array') {
      return TRUE;
    }
    if (strpos($checkedType, 'php:class:') !== FALSE) {
      if ($referenceType == 'php:type:object') {
        return TRUE;
      }
      if (strpos($referenceType, 'php:class:') !== FALSE) {
        $checkedClass = substr($checkedType, 10);
        if ($checkedClass instanceof $referenceType) {
          return TRUE;
        }
      }
    }
    if ($referenceType == 'php:index-type' && $checkedType == 'php:type:string') {
      return TRUE;
    }
    if (in_array($referenceType, array('php:type:number', 'php:index-type'))
        && strpos($checkedType, 'php:type:number:') !== FALSE) {
      return TRUE;
    }
    return ($checkedType == $referenceType);
  }

  /**
   * Extract a type parameter from the given @p $actualType, which is expected
   * to be a subtype of the given @p $referenceType. The @p $parameterName
   * value denotes the name of a type parameter in @p $referenceType, so if the
   * latter is for example "transformations:list<transformations:any T>" then
   * a @p $parameterName of "T" will yield the concrete subtype of
   * "transformations:any" in @p $actualType.
   *
   * If something goes wrong (for example, if @p $actualType is not a subtype
   * of @p $referenceType, or if the function is not yet implemented - which is
   * currently the case) then an exception is thrown.
   */
  public static function extractTypeParameter($actualType, $referenceType, $parameterName) {
    throw new Exception('Not yet implemented.');
  }
}



/**
 * A piece of data wrapped in an accessor object that provides access to either
 * an iterator for its child objects, or to the data itself. It is expected
 * that, once generated, this data is fixed and will not change anymore,
 * even if the operation that created this object would now return
 * different results.
 */
abstract class TfDataWrapper implements IteratorAggregate {
  protected $data;
  private $type;

  const ChildrenConcreteLeafs = 1;
  const ChildrenAlwaysWrapped = 2;
  const ChildrenMixed         = 3;

  const SerializationNotSupported = NULL;


  //
  // Static methods.
  //

  /**
  * Create and return a TfDataWrapper object for @p $data that best suits
  * the @p $data type. If @p $data is NULL, NULL will be returned.
  */
  public static function create($data) {
    if (is_null($data)) {
      return NULL;
    }
    $className = _transformations_highest_priority_data_wrapper($data);
    if (empty($className)) {
      // Last-line-of-defense fallback when no other classes match the data type.
      $className = 'TfConcreteData';
    }
    self::includeWrapper($className);
    return new $className($data);
  }

  /**
  * Create and return a TfDataWrapper object for @p $data that best suits the
  * @p $data type. If no wrapper is specifically suited, return @p $data itself,
  * not encapsulated in any TfDataWrapper object.
  */
  public static function createIfSuitableWrapperExists($data) {
    $className = _transformations_highest_priority_data_wrapper($data);
    if (!$className) {
      return $data;
    }
    self::includeWrapper($className);
    return new $className($data);
  }

  public static function includeWrapper($className) {
    $dataWrappers = transformations_data_wrapper_info();
    _transformations_include_extension($className, $dataWrappers);
  }

  /**
   * Recreate a data wrapper from the serializable value that was returned by
   * the export() function.
   */
  public static function import($exportedData) {
    // Expand compressed basic types ("optimize for the common case" reversion)
    if (is_string($exportedData) || is_numeric($exportedData) || is_bool($exportedData)) {
      $exportedData = array(
        'w' => 'TfConcreteData',
        'v' => $exportedData,
      );
    }

    $className = isset($exportedData['w' /* wrapper */]) ? $exportedData['w'] : FALSE;

    if (isset($exportedData['c' /* children */])) {
      module_load_include('inc', 'transformations', 'core/transformations.data.serialization');
      $exportedData['v' /* value */] = new TfChildrenUnserializationIterator(
        $exportedData['c']['k' /* keys */], $exportedData['c']['v' /* values */]
      );
    }
    // otherwise, $exportedData['v'] is already set anyways

    if ($className) {
      self::includeWrapper($className);
      $wrapper = new $className($exportedData['v' /* value */], TRUE);
    }
    else {
      $wrapper = TfDataWrapper::create($exportedData['v' /* value */]);
      if (is_null($wrapper)) {
        return NULL;
      }
    }

    if (isset($exportedData['t' /* type */])) {
      $wrapper->setType($exportedData['t']);
    }
    return $wrapper;
  }


  //
  // Instance methods.
  //

  /**
   * Create a TfDataWrapper object with the given @p $data, whatever it may be:
   * concrete raw data, a result iterator, a handle, ...
   */
  final public function __construct($data) {
    if (is_null($data)) {
      throw new Exception('NULL may not be wrapped in a TfDataWrapper object.');
    }
    $importing = FALSE;

    if (func_num_args() > 1 && func_get_arg(1) == TRUE) { // hidden argument, kind of
      $importing = TRUE;
    }
    $this->initialize($data, $importing);
  }

  /**
   * Subclasses of TfDataWrapper cannot override the constructor, so that the
   * exact parameter signature is guaranteed. If there is still something to
   * initialize, subclasses can override this function which is called from
   * the constructor with the same arguments.
   * (Remember to call parent::initialize(), though.)
   */
  protected function initialize($data, $importing = FALSE) {
    $this->data = $data;
    $this->type = NULL;
  }

  /**
   * Create a variable that can be run through serialize() and unserialize()
   * and afterwards can be passed to TfDataWrapper::import() in order to
   * recreate the same data wrapper again - or a similar one, in case the
   * original wrapper provided access to data that can't be unserialized in
   * its original form.
   */
  final public function export() {
    $exportedData = array();
    $data = $this->serializableData();

    if (isset($data) /* $data !== TfDataWrapper::SerializationNotSupported */) {
      $exportedData['w' /* wrapper */] = get_class($this);
      $exportedData['v' /* value */] = $data;
    }
    elseif (!$this->isIteratable()) {
      $exportedData['v' /* value */] = $this->data();
    }
    else {
      foreach ($this->children(self::ChildrenAlwaysWrapped) as $key => $childWrapper) {
        $exportedData['c' /* children */]['k' /* keys */][] = $key;
        $exportedData['c' /* children */]['v' /* values */][] =
          is_null($childWrapper) ? NULL : $childWrapper->export();
      }
    }

    if (isset($this->type)) {
      $exportedData['t' /* type */] = $this->type;
    }

    // Optimize for the common case: require less space for basic types.
    if (is_array($exportedData) && !isset($exportedData['f']) && isset($exportedData['v'])
        && (!isset($exportedData['w']) || $exportedData['w'] == 'TfConcreteData')) {
      if (is_string($exportedData['v']) || is_numeric($exportedData['v']) || is_bool($exportedData['v'])) {
        $exportedData = $exportedData['v'];
      }
    }
    return $exportedData;
  }

  /**
   * Provide a piece of data that will be run through serialize() by the
   * export() method, and will be passed in unserialized form to the
   * constructor of this class in order to restore the original wrapper state.
   *
   * If the data cannot (directly) be serialized, return
   * TfDataWrapper::SerializationNotSupported in order to make
   * serializeToArray() fetch the values from data() or children() by itself
   * (and restore them into a different wrapper than this one).
   */
  protected function serializableData() {
    return TfDataWrapper::SerializationNotSupported;
  }

  /**
   * The default Transformations data type for the given (raw, unwrapped) data
   * that is used as result of type() unless a custom type is set.
   */
  abstract protected function defaultType();

  /**
   * The Transformations-specific data type that applies to this element.
   * This is a string containing of the actual type (e.g. 'php:type:string',
   * 'php:type:number:integer', 'transformations:list', 'drupal:node:object').
   */
  final public function type() {
    if (!isset($this->type)) {
      $this->type = TfDataWrapper::defaultType();
    }
    return $this->type;
  }

  /**
   * Return TRUE if this data wrapper wraps a subtype of @p $type,
   * or FALSE otherwise.
   */
  final public function subtypeOf($type) {
    return TfDataType::subtypeOf($this->type(), $type);
  }

  /**
   * Specify a more exact data type that applies to this piece of data but is
   * still compatible with the original one provided by the defaultType()
   * method. Passing NULL unsets a custom type and returns to the original one.
   */
  final public function setType($type) {
    $this->type = $type;
  }

  /**
   * Retrieve the raw data. May only be called when type() derives from a
   * PHP type (i.e. is not an abstract "transformations:list" or similar) and
   * the caller knows how to deal with it. If the returned data is an object,
   * take care not to modify it as that would break the immutability
   * assumptions for TfDataWrapper objects.
   */
  public function data() {
    throw new Exception('TfDataWrapper::data() is not implemented by ' . get_class($this) . ', which probably means you called it without checking type() first. (Alternatively, this wrapper class might be lacking a data() implementation even though it claims to support the given data type.)');
  }

  /**
   * Return TRUE if this element is a subtype of "transformations:list" and
   * thus will return a valid iterator when the getIterator() method is called.
   * Return FALSE if this element is not iteratable, in which case data() must
   * always return a valid value.
   *
   * In theory, the result of this method could be retrieved by comparing types.
   * In practice, it's simple enough to ask subclasses to override it, which
   * makes for far better performance.
   */
  public function isIteratable() {
    return TRUE;
  }

  /**
   * Implementation of IteratorAggregate::getIterator():
   * An alias for the children(TfDataWrapper::ChildrenConcreteLeafs) method.
   */
  public function getIterator() {
    return $this->children(TfDataWrapper::ChildrenConcreteLeafs);
  }

  /**
   * Return an iterator that yields the child elements of this wrapper, with
   * the element label as key. If this TfDataWrapper represents a leaf element,
   * this method always returns an empty iterator.
   *
   * You can use the returned iterator like this:
   * @code
   *   foreach ($wrapper->children($mode) as $label => $element) {
   *     // process the data
   *   }
   * @endcode
   *
   * If you just want the standard TfDataWrapper::ChildrenConcreteLeafs
   * iteration mode, you can skip the method call altogether and write this:
   * @code
   *   foreach ($wrapper as $label => $element) {
   *     // process the data
   *   }
   * @endcode
   *
   * @param $mode
   *   Determines the format of the returned element values. This can be one
   *   of the following constants:
   *
   *   - TfDataWrapper::ChildrenConcreteLeafs: Leaf children appear as
   *       concrete values (i.e. the result value of data()), whereas element
   *       collections remain TfDataWrapper instances that can again be
   *       traversed with children().
   *   - TfDataWrapper::ChildrenAlwaysWrapped: All child elements are returned
   *       as TfDataWrapper instances. This might be useful when checking
   *       custom types of leaf elements, which is not possible when using
   *       the ChildrenConcreteLeafs mode. Note that NULL values still remain
   *       as such, they are never wrapped.
   *   - TfDataWrapper::ChildrenMixed: Child elements are returned as the
   *       data wrapper prefers them - concrete raw data or already-wrapped
   *       data is possible for both leaf and element collection children.
   *       Mostly for internal usage.
   */
  public function children($mode = TfDataWrapper::ChildrenConcreteLeafs) {
    if (!$this->isIteratable()) {
      return new EmptyIterator();
    }
    if ($mode == TfDataWrapper::ChildrenConcreteLeafs) {
      return new TfDataWrapperConcreteLeafIterator($this->rawChildElementIterator());
    }
    if ($mode == TfDataWrapper::ChildrenAlwaysWrapped) {
      return new TfDataWrapperAlwaysWrappedIterator($this->rawChildElementIterator());
    }
    if ($mode == TfDataWrapper::ChildrenMixed) {
      return $this->rawChildElementIterator();
    }
    return new EmptyIterator(); // does not happen, except for invalid arguments
  }

  /**
   * Return an iterator that yields the child elements of this wrapper, with
   * the element label as key. Whether the child elements are returned by the
   * iterator as concrete raw data or as TfDataWrappers does not matter, the
   * caller is expected to handle this distinction gracefully.
   *
   * If this TfDataWrapper represents a leaf element, this method will never
   * be called.
   */
  protected function rawChildElementIterator() {
    return new EmptyIterator();
  }
}

/**
 * Direct data access to concrete data, without further wrappers.
 * This class should be used as base class for all data types that can be
 * directly serialized (and more importantly, unserialized as well!).
 */
class TfConcreteData extends TfDataWrapper {
  public function data() {
    return $this->data;
  }

  protected function serializableData() {
    return TfDataWrapper::SerializationNotSupported;
    if (is_array($this->data) || is_resource($this->data)) {
      return TfDataWrapper::SerializationNotSupported;
    }
    if (is_object($this->data) && !($this->data instanceof Serializable)) {
      return TfDataWrapper::SerializationNotSupported;
    }
    return $this->data;
  }

  protected function defaultType() {
    return TfDataType::phpType($data);
  }

  public function isIteratable() {
    return FALSE;
  }
}

/**
 * An iterator that unwraps leaf elements from TfDataWrapper objects in case
 * the data is wrapped, and wraps collection data in TfDataWrapper objects
 * in case they are not already wrapped. Such an iterator is used as result
 * value of TfDataWrapper::children(TfDataWrapper::ChildrenConcreteLeafs).
 */
class TfDataWrapperConcreteLeafIterator extends IteratorIterator {
  public function current() {
    $element = parent::current();

    if (!is_object($element) || !($element instanceof TfDataWrapper)) {
      $element = TfDataWrapper::createIfSuitableWrapperExists($element);
    }
    if (is_object($element) && $element instanceof TfDataWrapper) {
      if (!$element->isIteratable()) {
        $element = $element->data();
      }
    }
    return $element;
  }
}

/**
 * An iterator that wraps elements from TfDataWrapper objects in case the data
 * is not wrapped. Such an iterator is used as result value of
 * TfDataWrapper::children(TfDataWrapper::ChildrenAlwaysWrapped).
 */
class TfDataWrapperAlwaysWrappedIterator extends IteratorIterator {
  public function current() {
    $element = parent::current();

    if (!is_object($element) || !($element instanceof TfDataWrapper)) {
      $element = TfDataWrapper::create($element);
    }
    return $element;
  }
}



/**
 * Determine which TfDataWrapper class is best suited for @p $data that
 * best suits the @p $data type. If no wrapper is specifically suited,
 * return FALSE (which means that TfConcreteData is the only viable wrapper
 * choice, if @p $data should be wrapped at all).
 */
function _transformations_highest_priority_data_wrapper($data) {
  $priorities = module_invoke_all('transformations_data_wrapper_priority', $data);
  $sortedPriorities = array();

  foreach ($priorities as $className => $priority) {
    $sortedPriorities[$priority][] = $className;
  }
  // Sort the array, so that the highest priority classes go first.
  krsort($sortedPriorities);

  if (empty($sortedPriorities)) {
    return FALSE;
  }
  // All of the $highestPriority classes have the same priority, so choose
  // any random class out of that array (hopefully there is only one) -
  // we just pick the first one and return a new instance of that class.
  $highestPriority = reset($sortedPriorities);
  return reset($highestPriority);
}
