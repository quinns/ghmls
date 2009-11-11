<?php
// $Id: transformations.data.widget.inc,v 1.10 2009/06/23 22:20:09 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Retrieve the most appropriate form element for a given operation input.
 * FALSE is returned if the given input key is not available for the operation.
 */
class TfDataWidget {
  /**
   * Retrieve the most appropriate form element for a given operation input.
   *
   * @param $operation
   *   The operation for whose input you want to find an appropriate widget.
   * @param $inputKey
   *   The input key inside the above operation that specifies the exact input.
   */
  function forInput(TfOperation $operation, $inputKey) {
    if (!in_array($inputKey, $operation->inputKeys())) {
      return FALSE;
    }
    $type = $operation->inputProperty($inputKey, 'expectedType');
    $typeHints = $operation->inputProperty($inputKey, 'dataWidgetTypeHints');
    $element = TfDataWidget::forDataType($type, $typeHints, $operation->input($inputKey));

    $element['#title'] = check_plain($operation->inputProperty($inputKey, 'label'));
    $element['#required'] = $operation->inputProperty($inputKey, 'required');

    if (!empty($element['#default_value']) && !$operation->isInputSet($inputKey)) {
      $value = new TfDataWidgetValue($element);
      $value->setUseDefault(TRUE);
      $value->setValue($element['#default_value']);
      $element['#default_value'] = $value;
    }

    $description = check_plain($operation->inputProperty($inputKey, 'description'));
    $description = preg_replace('/\\n/', '<br/>', $description);
    if (!empty($description)) {
      $element['#description'] = $description;
    }
    return $element;
  }

  /**
   * Retrieve the most appropriate form element for a given data type.
   */
  function forDataType($type, $typeHints = array(), TfDataWrapper $data = NULL) {
    $highest_priority = 0;
    $elements = array();

    foreach (module_implements('transformations_data_widgets') as $module) {
      $function = $module . '_transformations_data_widgets';
      $moduleElements = $function($type, $typeHints, $data);
      if (!is_array($moduleElements)) {
        continue;
      }

      foreach ($moduleElements as &$element) {
        if (!empty($element['#file']) && empty($element['#path'])) {
          $element['#path'] = drupal_get_path('module', $module);
        }
        $elements[] = $element;
      }
    }
    drupal_alter('transformations_data_widgets', $elements, $type);

    $sorted_elements = array();

    foreach ($elements as $key => &$element) {
      $sorted_elements[$element['#priority']][] = $element;
      unset($element['#priority']);
    }
    // Sort the array, so that the highest priority widgets go first.
    krsort($sorted_elements);

    if (empty($sorted_elements)) {
      $element = array(
        '#type' => 'textarea',
        '#transformations_handles_data' => FALSE,
        '#transformations_value_callback' => 'transformations_data_widget_php_code_value',
        '#required' => TRUE,
        '#title' => t('PHP code'),
        '#description' => t('<p>Enter PHP code to generate the desired value, and pass it back with a return statement. Example:</p><p><code>return $desired_value;</code></p>'),
      );
    }
    else {
      // All of the $highest_priority_widgets have the same priority, so choose
      // any random class out of that array - we just pick the first one.
      $highest_priority_elements = reset($sorted_elements);
      $element = reset($highest_priority_elements);
    }

    // Create the actual data widget that wraps the data-specific one and
    // provides additional logic for data management, validation and includes.
    $wrapper_element = array(
      '#type' => 'transformations_data_widget',
    );
    if (!empty($element['#file'])) {
      $wrapper_element['#transformations_include'] = array(
        'file' => $element['#file'],
        'path' => $element['#path'],
      );
      unset($element['#file']);
      unset($element['#path']);
    }
    if (isset($data)) {
      $wrapper_element['#default_value'] = $data;
    }

    $wrapper_element['#transformations_handles_data'] =
      isset($element['#transformations_handles_data'])
        ? $element['#transformations_handles_data']
        : TRUE;

    // Migrate a couple more properties from the child widget to the wrapper.
    $wrapper_properties = array(
      '#transformations_handles_data',
      '#transformations_value_callback',
      '#transformations_from_data_wrapper_callback',
    );
    foreach ($wrapper_properties as $key) {
      if (isset($element[$key])) {
        $wrapper_element[$key] = $element[$key];
        unset($element[$key]);
      }
    }

    $wrapper_element['#transformations_child_element'] = $element;
    return $wrapper_element;
  }
}

/**
 * The return type of a 'transformations_data' widget.
 *
 * A class that stores an input widget's state for lazy evaluation, and only
 * retrieves the "real" value when being asked for it - as opposed to Form API
 * which is not able to distinguish between internal state (e.g. PHP code)
 * and result value (e.g. value resulting from that PHP code).
 */
class TfDataWidgetValue implements ArrayAccess {
  public $value;
  public $elementValue;
  public $valueCallback;
  public $fromWrapperCallback;
  public $includeFile;
  public $useDefault;

  public function __construct($element = NULL) {
    if (isset($element['#transformations_from_data_wrapper_callback'])
        && $element['#transformations_handles_data']) {
      $this->fromWrapperCallback = $element['#transformations_from_data_wrapper_callback'];
    }
    else {
      $this->fromWrapperCallback = NULL;
    }
    $this->valueCallback = isset($element['#transformations_value_callback'])
      ? $element['#transformations_value_callback']
      : NULL;
    $this->includeFile = isset($element['#transformations_include'])
      ? $element['#transformations_include']
      : NULL;
    $this->useDefault = FALSE;
    $this->value = NULL;
    $this->elementValue = NULL;
  }

  /**
   * Return TRUE if the default value should be used instead of the results of
   * the value() method, or FALSE otherwise.
   */
  public function useDefault() {
    return $this->useDefault;
  }
  public function setUseDefault($useDefault) {
    $this->useDefault = (boolean) $useDefault;
  }

  /**
   * Retrieve the value of the given element, run through the final
   * (Transformations-specific) value callback.
   */
  public function value() {
    if (!empty($this->useDefault)) {
      return NULL;
    }
    return $this->value;
  }

  /**
   * Set the actual TfDataWrapper value of the widget (intended for usage
   * from outside). The value is automatically converted to the widget's
   * internal data format by calling the "from data wrapper" callback.
   */
  public function setValue($value) {
    $this->value = $value;

    if (!is_object($value) || !($value instanceof TfDataWrapper)) {
      // Wrap the value in a TfDataWrapper if it's not already wrapped.
      // Slightly inefficient, but convenient for widgets as the callback
      // can rely on the data to be always passed in a consistent format.
      $value = TfDataWrapper::create($value);
    }

    if (empty($this->fromWrapperCallback)) {
      // Use TfDataWrapper::data() to extract the data. Widgets that don't
      // find this sufficient should provide their own callback.
      $value = isset($value) ? $value->data() : NULL;
    }
    else {
      // Use the "from data wrapper" callback if the widget has provided one.
      $this->includeFile();
      $fromWrapper = $this->fromWrapperCallback;
      $value = $fromWrapper($value);
    }

    $this->elementValue = $value;
  }

  private function includeFile() {
    if (!empty($this->includeFile)) {
      require_once './' . $this->includeFile['path'] . '/' . $this->includeFile['file'];
    }
  }

  /**
   * Retrieve the value of the given element.
   */
  public function childElementValue() {
    return $this->elementValue;
  }

  /**
   * Set the internal value of the widget (intended for usage from within
   * Form API and the widget).
   */
  public function setChildElementValue($value) {
    $this->elementValue = $value;

    if (empty($this->valueCallback)) {
      $this->value = $this->elementValue;
    }
    else {
      $this->includeFile();
      $valueCallback = $this->valueCallback;
      $this->value = $valueCallback($this->elementValue);
    }
  }

  // Form API treats everything in '#tree' elements as array, so let's work
  // around this defiancy by implementing the ArrayAccess interface.
  public function offsetExists($key) {
    return in_array($key, array('widget', 'use_default'));
  }

  public function offsetGet($key) {
    if ($key == 'widget') {
      $childElementValue = $this->childElementValue();
      if (is_array($childElementValue)) {
        return new TfDataWidgetArray($this); // Ugly.
      }
      return $childElementValue;
    }
    elseif ($key == 'use_default') {
      return $this->useDefault();
    }
    throw new Exception('Invalid key.');
  }

  public function offsetSet($key, $value) {
    if ($key == 'widget') {
      $this->setChildElementValue($value);
    }
    elseif ($key == 'use_default') {
      $this->setUseDefault($value);
    }
    else {
      throw new Exception('Invalid key.');
    }
  }

  public function offsetUnset($key) {
    $this->offsetSet($key, ($key == 'use_default') ? FALSE : NULL);
  }
}

/**
 * An object that wraps the element value array returned by TfDataWidgetValue's
 * offsetGet() method, and ensures that recursive writes will relay back to
 * TfDataWidgetValue's offsetSet().
 */
class TfDataWidgetArray implements ArrayAccess {
  private $dataWidgetValue;
  private $keysFromOriginal;

  public function __construct($dataWidgetValue, $keysFromOriginal = array()) {
    $this->dataWidgetValue = $dataWidgetValue;
    $this->keysFromOriginal = $keysFromOriginal;
  }

  // Form API treats everything in '#tree' elements as array, so let's work
  // around this defiancy by implementing the ArrayAccess interface.
  public function offsetExists($key) {
    $array = $this->dataWidgetValue->childElementValue();
    $deepArray = &$this->deepArrayReference($array);
    return array_key_exists($key, $deepArray);
  }

  public function offsetGet($key) {
    $array = $this->dataWidgetValue->childElementValue();
    $deepArray = &$this->deepArrayReference($array);
    if (is_array($deepArray[$key])) {
      $keys = $this->keysFromOriginal;
      $keys[] = $key;
      return TfDataWidgetArray($dataWidgetValue, $keys);
    }
    return $deepArray[$key];
  }

  public function offsetSet($key, $value) {
    $array = $this->dataWidgetValue->childElementValue();
    $deepArray = &$this->deepArrayReference($array);
    $deepArray[$key] = $value;
    $this->dataWidgetValue->setChildElementValue($array);
  }

  public function offsetUnset($key) {
    $array = $this->dataWidgetValue->childElementValue();
    $deepArray = &$this->deepArrayReference($array);
    unset($deepArray[$key]);
    $this->dataWidgetValue->setChildElementValue($array);
  }

  private function &deepArrayReference(&$array, &$keys = NULL) {
    if (!isset($keys)) {
      $keys = $this->keysFromOriginal;
    }
    if (empty($keys)) {
      return $array;
    }
    $key = array_shift($keys);
    return $this->elementReference($array[$key], $keys);
  }
}

/**
 * Implementation of hook_transformations_data_widgets():
 * Provide a set of default widgets for the most common data types.
 */
function transformations_transformations_data_widgets($type, $typeHints = array(), TfDataWrapper $data = NULL) {
  static $path;

  if (!isset($path)) {
    $path = drupal_get_path('module', 'transformations') . '/core';
  }
  $widgets = array();

  if (TfDataType::subtypeOf($type, 'php:type:string')) {
    if (!empty($typeHints['options'])) {
      $widgets[] = array(
        '#type' => 'select',
        '#options' => $typeHints['options'],
        '#priority' => 5,
        '#required' => TRUE,
      );
    }
    $is_multiline = !empty($typeHints['string:multiline']);
    $widgets[] = array(
      '#type' => !empty($is_multiline) ? 'textarea' : 'textfield',
      '#priority' => 1,
      '#required' => TRUE,
    );
  }

  if (TfDataType::subtypeOf($type, 'php:type:number')) {
    $widget = array(
      '#type' => 'textfield',
      '#priority' => 1,
      '#file' => 'transformations.data.widget.inc',
      '#path' => $path,
      '#element_validate' => 'transformations_data_widget_decimal_validate',
    );
    if (TfDataType::subtypeOf($type, 'php:type:number:integer')) {
      $widget['#element_validate'] = 'transformations_data_widget_integer_validate';
      $widget['#transformations_value_callback'] = 'transformations_data_widget_integer_value';
    }
    elseif (TfDataType::subtypeOf($type, 'php:type:number:float')) {
      $widget['#transformations_value_callback'] = 'transformations_data_widget_float_value';
    }
    elseif (TfDataType::subtypeOf($type, 'php:type:number:double')) {
      $widget['#transformations_value_callback'] = 'transformations_data_widget_double_value';
    }
    $widgets[] = $widget;
  }

  if (TfDataType::subtypeOf($type, 'php:type:boolean')) {
    $widgets[] = array(
      '#type' => 'checkbox',
      '#priority' => 1,
      '#file' => 'transformations.data.widget.inc',
      '#path' => $path,
      '#transformations_value_callback' => 'transformations_data_widget_boolean_value',
    );
  }

  if (TfDataType::subtypeOf($type, 'transformations:pipeline')) {
    $widgets[] = array(
      '#type' => 'transformations_pipeline_select',
      '#priority' => 5,
      '#file' => 'transformations.data.widget.inc',
      '#path' => $path,
      '#transformations_from_data_wrapper_callback' => 'transformations_data_widget_pipeline_from_wrapper',
      '#transformations_value_callback' => 'transformations_data_widget_pipeline_value',
    );
  }

  if (TfDataType::subtypeOf($type, 'transformations:operation')) {
    $options = array();
    foreach (transformations_operation_info() as $className => $info) {
      if (!empty($info['abstract'])) {
        continue;
      }
      $options[$info['category']][$className] = check_plain($info['label']);
    }
    // Put the "Other" category at the bottom of the list.
    $other_category = t('Other');
    if (isset($options[$other_category])) {
      $other_options = $options[$other_category];
      unset($options[$other_category]);
      $options[$other_category] = $other_options;
    }

    $widgets[] = array(
      '#type' => 'select',
      '#options' => $options,
      '#priority' => 5,
      '#required' => TRUE,
    );
  }
  return $widgets;
}

/**
 * Element validation callback for numbers with decimal places allowed.
 */
function transformations_data_widget_decimal_validate(&$element, &$form_values) {
  if (!is_numeric($element['#value'])) {
    form_error($element, t('%element must contain a valid number.', array(
      '%element' => $element['#title'],
    )));
  }
}

/**
 * Widget validation callback for numbers with decimal places allowed.
 */
function transformations_data_widget_integer_validate(&$element, &$form_values) {
  if (((int) $element['#value']) != $element['#value']) {
    form_error($element, t('%element must contain a valid integer number.', array(
      '%element' => $element['#title'],
    )));
  }
}

function transformations_data_widget_php_code_value($element_value) {
  return empty($element_value) ? NULL : eval($element_value);
}

function transformations_data_widget_integer_value($element_value) {
  return (int) $element_value;
}

function transformations_data_widget_float_value($element_value) {
  return (float) $element_value;
}

function transformations_data_widget_double_value($element_value) {
  return (double) $element_value;
}

function transformations_data_widget_boolean_value($element_value) {
  return (boolean) $element_value;
}


//
// Internal form element callbacks.
//

/**
 * Form value callback for the widget element.
 * (Redirected from transformations.module, hence the leading underscore.)
 */
function _transformations_data_widget_value($element, $edit = FALSE) {
  $value = new TfDataWidgetValue($element);

  // When $_POST is empty, the function is called with a single argument.
  if (func_num_args() == 1) {
    if (isset($element['#default_value'])) {
      $defaultValue = $element['#default_value'];

      if (is_object($defaultValue) && $defaultValue instanceof TfDataWidgetValue) {
        return $defaultValue;
      }
      $value->setUseDefault(FALSE);
      $value->setValue($defaultValue);
      return $value;
    }
    // No default value given -> default to the default value (if the widget is
    // not required, otherwise this setting is ignored anyways).
    $value->setUseDefault(TRUE);
    $value->setValue(NULL);
    return $value;
  }

  // The form has been posted, so return the actual (lazy-wrapped) value. The
  // isset() check is done because checkbox widgets lack post values for FALSE.
  $value->setChildElementValue(isset($edit['widget']) ? $edit['widget'] : NULL);
  $value->setUseDefault(isset($edit['use_default']) ? $edit['use_default'] : FALSE);
  return $value;
}

/**
 * Process callback for the widget element.
 */
function transformations_data_widget_process($element, $edit, &$form_state, $complete_form) {
  $element['#tree'] = TRUE;

  // Due to the value callback, the value here is a TfDataWidgetValue object.
  $widgetValue = $element['#value'];

  $element['widget'] = $element['#transformations_child_element'];

  if (isset($element['#title'])) {
    $element['widget']['#title'] = $element['#title'];
    unset($element['#title']);
  }

  if (isset($element['#description'])) {
    if (!isset($element['widget']['#description'])) {
      $element['widget']['#description'] = '';
    }
    $element['widget']['#description'] .= ' <p>' . $element['#description'] . '</p>';
    unset($element['#description']);
  }

  // Wrap the original '#element_validate' in our own wrapper that only
  // validates if the default value is not used.
  if (!empty($element['widget']['#element_validate'])) {
    $element['#element_validate'][] = 'transformations_data_widget_validate';
    $element['#transformations_validate'] = $element['widget']['#element_validate'];
    unset($element['widget']['#element_validate']);
  }

  if ($element['#transformations_handles_data']) {
    $element['widget']['#default_value'] = $widgetValue->childElementValue();
  }
  elseif (!$widgetValue->useDefault()) {
    $value = $widgetValue->value();

    if (!isset($element['widget']['#description'])) {
      $element['widget']['#description'] = '';
    }
    $element['widget']['#description'] .= ' <p>' . t('This input widget is not capable to display the currently assigned value and will replace it when the value is stored. To view the current value, have a look at the area below.') . '</p>';

    if (isset($value)) {
      $dumper = TfOperation::load('TfDumpDataToPlaintext');
      $dumper->setInput('data', $value);
      $dump = $dumper->output()->at('plaintext')->data();
    }
    else {
      $dump = 'NULL';
    }

    $element['current'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#title' => t('Current value'),
    );
    $element['current']['value'] = array(
      '#type' => 'markup',
      '#value' => empty($dump)
        ? t('empty data')
        : '<pre>' . check_plain($dump) . '</pre>',
    );
  }

  if ($element['#required']) {
    $element['use_default'] = array(
      '#type' => 'value',
      '#value' => FALSE,
    );
    $widgetValue->setUseDefault(FALSE);
  }
  else {
    $element['use_default'] = array(
      '#type' => 'checkbox',
      '#default_value' => $widgetValue->useDefault(),
      '#title' => t('Use the default value for this input.'),
      '#prefix' => '<div style="margin-left: 1.5em;">',
      '#suffix' => '</div>',
    );
  }

  // Make the "required" star appear for the child widget even though it's not
  // formally required.
  $element['#transformations_child_required'] = $element['#required'];
  $element['#required'] = FALSE;
  $element['#pre_render'] = array('transformations_data_widget_pre_render');

  return $element;
}

/**
 * Validation callback for the widget element.
 */
function transformations_data_widget_validate(&$element, &$form_state) {
  if ($element['use_default']['#value']) {
    return; // the actual value doesn't matter, so skip widget validation
  }
  if (!empty($element['#transformations_include'])) {
    $include = $element['#transformations_include'];
    require_once './' . $include['path'] . '/' . $include['file'];
  }
  $validate = $element['#transformations_validate'];
  $validate($element['widget'], $form_state);
}

/**
 * Pre-render callback: Make the child element look like it's required.
 * (Which is the case actually, just doesn't go through '#required'.)
 */
function transformations_data_widget_pre_render($element) {
  $element['widget']['#required'] = $element['#transformations_child_required'];
  return $element;
}

/**
 * Theme function for the wrapper widget.
 */
function theme_transformations_data_widget($element) {
  return theme('form_element', $element, $element['#children']);
}


/**
 * "Element value from data wrapper" callback for the pipeline selection element.
 */
function transformations_data_widget_pipeline_from_wrapper($wrapper) {
  if (!isset($wrapper)) {
    return array('select' => NULL);
  }
  $pipeline = $wrapper->data();
  if (is_numeric($pipeline)) {
    return array('select' => $pipeline);
  }
  elseif (is_object($pipeline) && $pipeline instanceof TfPipeline) {
    $pipelineId = $pipeline->id();
    if (!empty($pipelineId)) {
      return array('select' => $pipelineId);
    }
    return array('select' => 'current_embedded', 'current_embedded' => serialize($pipeline));
  }
  throw new Exception('Invalid value given for the pipeline selection widget.');
}

/**
 * "Data wrapper from element value" callback for the pipeline selection element.
 */
function transformations_data_widget_pipeline_value($element_value) {
  if (empty($element_value['select'])) {
    return NULL;
  }
  if ($element_value['select'] == 'new') {
    $pipeline = new TfPipeline(t('Embedded pipeline'));
    $wrapper = TfDataWrapper::create($pipeline);
  }
  elseif ($element_value['select'] == 'current_embedded') {
    $pipeline = unserialize($element_value['current_embedded']);
    $wrapper = TfDataWrapper::create($pipeline);
  }
  else {
    $wrapper = new TfConcreteData($element_value['select']);
  }
  $wrapper->setType('transformations:pipeline');
  return $wrapper;
}

/**
 * Process callback for the pipeline selection element.
 */
function transformations_pipeline_select_process($element, $edit, &$form_state, $complete_form) {
  $element['#tree'] = TRUE;

  $result = db_query('SELECT pipeline_id, name FROM {transformations_pipeline}');
  $options = array();

  if (!empty($element['#value']['current_embedded'])) {
    $options['current_embedded'] = t('Current embedded pipeline');
  }

  while ($pipeline = db_fetch_object($result)) {
    $options[$pipeline->pipeline_id] = check_plain($pipeline->name);
  }
  $options['new'] = t('New embedded pipeline');

  if (empty($element['#value']['select'])) {
    $default_option = reset(array_keys($options));
  }
  else {
    $default_option = $element['#value']['select'];
  }

  $element['select'] = array(
    '#type' => 'select',
    '#options' => $options,
    '#default_value' => $default_option,
  );

  if (!empty($element['#value']['current_embedded'])) {
    $element['current_embedded'] = array(
      '#type' => 'value',
      '#value' => $element['#value']['current_embedded'],
    );
  }
  return $element;
}

/**
 * Theme function for the pipeline selection element.
 */
function theme_transformations_pipeline_select($element) {
  return theme('form_element', $element, $element['#children']);
}
