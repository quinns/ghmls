<?php
// $Id: transformations_ui.admin.inc,v 1.8 2009/06/23 19:15:20 jpetso Exp $
/**
 * @file
 * Transformations UI -
 * An interface for managing transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Form to list and manage all available pipelines.
 */
function transformations_ui_pipelines_list(&$form_state) {
  $form = array();
  $rows = array();
  $header = array('Pipeline', 'Manage');

  foreach (transformations_ui_pipeline_options() as $id => $name) {
    $action_links = array();
    $action_links[] = l(t('Edit'), 'admin/build/transformations/' . $id);
    $action_links[] = l(t('Rename'), 'admin/build/transformations/' . $id .'/rename');
    $action_links[] = l(t('Execute'), 'admin/build/transformations/' . $id .'/execute');
    $action_links[] = l(t('Delete'), 'admin/build/transformations/' . $id . '/delete');

    $row = array($name, implode(' | ', $action_links));
    $rows[] = $row;
  }
  $form['pipeline_list'] = array(
    '#value' => empty($rows)
                ? t('No pipelines defined.')
                : theme('table', $header, $rows),
  );

  return $form;
}


/**
 * Form to rename a pipeline. This is also used as "Add pipeline" form,
 * in which case TRANSFORMATIONS_UI_FORM_CREATE is passed as @p $pipeline.
 */
function transformations_ui_pipeline_rename(&$form_state, $pipeline) {
  $creating_pipeline = ($pipeline === TRANSFORMATIONS_UI_FORM_CREATE);

  $form = array();
  $form['#pipeline_id'] = $creating_pipeline ? TRANSFORMATIONS_UI_FORM_CREATE : $pipeline->id();
  $form['#validate'][] = 'transformations_ui_pipeline_rename_validate';

  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Pipeline name'),
    '#description' => t('Enter the name of the transformation pipeline here. It may consist of any characters, including whitespace. The only condition is that no other pipeline is assigned the same name.'),
    '#default_value' => ($creating_pipeline ? '' : $pipeline->name()),
    '#required' => TRUE,
  );

  if ($creating_pipeline) {
    $form['create'] = array(
      '#type' => 'submit',
      '#value' => t('Create'),
      '#submit' => array('transformations_ui_pipeline_rename_add'),
    );
  }
  elseif (!transformations_ui_is_pipeline_locked($pipeline)) {
    $form['update'] = array(
      '#type' => 'submit',
      '#value' => t('Update'),
      '#submit' => array('transformations_ui_pipeline_rename_update'),
    );
  }

  if (!$creating_pipeline) {
    $form['cancel'] = array(
      '#type' => 'submit',
      '#value' => transformations_ui_is_pipeline_locked($pipeline)
                  ? t('Back') : t('Cancel'),
      '#submit' => array('transformations_ui_goto_submit'),
      '#goto_path' => transformations_ui_pipeline_path($pipeline),
    );
  }
  return $form;
}

/**
 * Validation callback for a pipeline name textfield.
 */
function transformations_ui_pipeline_rename_validate($form, &$form_state) {
  // Make sure that no other pipeline has been assigned the same name already.
  // This doesn't matter to the database, but is purely a question of usability
  // in order to prevent confusion.
  if (TfPipeline::existsName($form_state['values']['name'], $form['#pipeline_id'])) {
    form_error($form['name'], t('There already exists another pipeline with the same name. Please assign a different name to this one.'));
  }
}

/**
 * Submit callback for the initial creation of a pipeline with the name
 * given in the form values.
 */
function transformations_ui_pipeline_rename_add($form, &$form_state) {
  $pipeline = new TfPipeline($form_state['values']['name']);
  transformations_ui_pipeline_persist($pipeline);
  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);
}

/**
 * Submit callback for the "Update" button on the "Rename pipeline" form.
 */
function transformations_ui_pipeline_rename_update($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);

  if (!$pipeline) {
    drupal_set_message(t('Error loading the pipeline.'), 'error');
    return;
  }
  $pipeline->setName($form_state['values']['name']);
  transformations_ui_pipeline_persist($pipeline);

  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);
}

/**
 * Page to delete a transformation.
 */
function transformations_ui_pipeline_delete_confirm(&$form_state, $pipeline) {
  $form_state['pipeline_id'] = $pipeline->id();
  $form = array();

  $cancel = 'admin/build/transformations';
  if (!empty($_REQUEST['cancel'])) {
    $cancel = $_REQUEST['cancel'];
  }

  $title = t('Are you sure you want to delete the pipeline %name?', array(
    '%name' => $pipeline->name(),
  ));
  $desc = t('Deleting a data transformation pipeline cannot be undone.');

  return confirm_form($form, $title, $cancel, $desc, t('Delete'), t('Cancel'));
}

/**
 * Submit handler to delete a pipeline.
 */
function transformations_ui_pipeline_delete_confirm_submit($form, &$form_state) {
  $pipeline = TfPipeline::load($form_state['pipeline_id']);
  if ($pipeline) {
    $pipeline->deleteFromDatabase();
  }
  drupal_set_message(t('The pipeline %name has been deleted.', array(
    '%name' => $pipeline->name(),
  )));
  $form_state['redirect'] = 'admin/build/transformations';
}


/**
 * Form callback for the "Add operation" page at
 * 'admin/build/transformations/%transformations_ui_persisted_pipeline/add-operation'.
 */
function transformations_ui_pipeline_operation_add(&$form_state, TfPipeline $pipeline) {
  $form = array();
  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);
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

  $form['className'] = array(
    '#type' => 'select',
    '#title' => t('Operation'),
    '#options' => $options,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Next',
    '#submit' => array('transformations_ui_pipeline_operation_add_submit'),
  );
  return $form;
}

/**
 * Submit callback for the "Add operation" form's "Next" button.
 */
function transformations_ui_pipeline_operation_add_submit($form, &$form_state) {
  $className = $form_state['values']['className'];
  $info = transformations_operation_info($className);

  if (!$info) {
    drupal_set_message(t('Invalid operation choice (%op), please try again.', array(
      '%op' => $form_state['values']['className'],
    )), 'error');
    return;
  }
  $operation = TfOperation::load($className);

  if (!$operation) {
    drupal_set_message(t('Operation %operation could not be created.', array(
      '%operation' => $className,
    )), 'error');
    return;
  }
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  if (!$pipeline) {
    drupal_set_message(t('Error loading pipeline.'), 'error');
  }
  $label = $operation->uiName();

  $operationId = $pipeline->addOperation($operation);
  $pipeline->setOperationProperty($operationId, 'label', $label);
  transformations_ui_pipeline_persist($pipeline);

  drupal_set_message(t('Added "@label" operation to the pipeline.', array(
    '@label' => $label,
  )));
  unset($form_state['storage']); // a.k.a. "don't rebuild the form please"
  $form_state['redirect'] = array(
    // Passing an array invokes drupal_goto() with call_user_func_array().
    transformations_ui_pipeline_path($pipeline),
    'new=' . drupal_urlencode($operationId),
  );
}


/**
 * Form callback for 'admin/build/transformations/%transformations_ui_persisted_pipeline/execute':
 * A form to execute/debug a pipeline.
 */
function transformations_ui_pipeline_execute(&$form_state, TfPipeline $pipeline) {
  $pipelineOperation = TfOperation::load('TfPipelineOperation');
  $pipelineOperation->setInput('pipeline', $pipeline);

  if (!$pipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation could not be initialized.'), 'error');
    return array();
  }
  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);

  $form['input'] = array(
    '#type' => 'fieldset',
    '#title' => t('Enter pipeline parameters'),
    '#description' => count($pipeline->targets(TfPipeline::Parameter))
      ? t('Enter all required pipeline parameters here, and press the "Execute" button to run the pipeline. You can choose to leave out non-required parameters, a default value will be used for those if no value is given. After pipeline execution, the pipeline output values will be displayed here.')
      : t('This pipeline doesn\'t require any parameters - press the "Execute" button to run the pipeline as is. After pipeline execution, the pipeline output values will be displayed here.'),
    '#collapsible' => TRUE,
    '#collapsed' => isset($form_state['storage']['input']),
  );

  foreach ($pipelineOperation->inputKeys() as $key) {
    if ($key == 'pipeline') {
      continue; // Filled out internally, see above.
    }
    if (isset($form_state['values'][$key])) {
      $pipelineOperation->setInput($key, $form_state['values'][$key]->value());
    }
    $form['input'][$key] = TfDataWidget::forInput($pipelineOperation, $key);
  }

  // A long namespaced element name reduces the chance of this element
  // clashing with any pipeline input name.
  $form['input']['transformations_ui_pipeline_execute_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Execute'),
    '#submit' => array('transformations_ui_pipeline_execute_submit'),
  );

  if (!isset($form_state['storage']['execution_successful'])) {
    return $form;
  }

  $form['result'] = array(
    '#type' => 'fieldset',
    '#title' => t('Result'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['result']['input'] = array(
    '#type' => 'fieldset',
    '#title' => t('Passed pipeline parameters'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#weight' => 1,
  );

  // Execute the pipeline and display its output if form values have been submitted.
  foreach ($form_state['storage']['input'] as $key => $input_dump) {
    $form['result']['input'][$key] = array(
      '#type' => 'item',
      '#title' => t('%key', array('%key' => $key)),
      '#value' => $input_dump,
    );
  }

  if (!$form_state['storage']['execution_successful']) {
    $form['result']['output'] = array(
      '#type' => 'markup',
      '#value' => $form_state['storage']['error_message'],
      '#prefix' => '<div class="messages error">',
      '#suffix' => '</div>',
      '#weight' => 0,
    );
    return $form;
  }
  $form['result']['output'] = array(
    '#type' => 'fieldset',
    '#title' => t('Pipeline outputs'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#weight' => 2,
  );

  if (isset($form_state['storage']['output'])) {
    foreach ($form_state['storage']['output'] as $key => $outputInfo) {
      $form['result']['output'][$key] = array(
        '#type' => 'item',
        '#title' => t('%label ("%key")', array(
          '%label' => $outputInfo['label'],
          '%key' => $key,
        )),
        '#value' => $outputInfo['dump'],
      );
    }
  }
  else {
    $form['result']['output']['empty'] = array(
      '#type' => 'item',
      '#value' => t('There were no output values for this pipeline operation.'),
    );
  }
  return $form;
}

/**
 * Submit callback for the "Execute" button on the "Execute pipeline" form.
 */
function transformations_ui_pipeline_execute_submit($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  $pipelineOperation = TfOperation::load('TfPipelineOperation');
  $pipelineOperation->setInput('pipeline', $pipeline);

  if (!$pipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation could not be initialized.'), 'error');
    return;
  }
  $dumper = TfOperation::load('TfDumpDataToPlaintext');
  $form_state['storage']['input'] = array();

  // Assign input values, a.k.a. pipeline parameters.
  foreach ($pipelineOperation->inputKeys() as $key) {
    if ($key == 'pipeline') {
      continue; // Filled out internally, see above.
    }
    $widgetValue = $form_state['values'][$key];

    if ($widgetValue->useDefault()) {
      $form_state['storage']['input'][$key] = t('(not set, using default value)');
    }
    else {
      $data = $widgetValue->value();
      $pipelineOperation->setInput($key, $data);
      $dumper->setInput('data', $data);
      $dump = $dumper->output()->at('plaintext')->data();
      $form_state['storage']['input'][$key] = '<pre>' . check_plain($dump) . '</pre>';
    }
  }

  $output = $pipelineOperation->output();
  $form_state['storage']['execution_successful'] = $output->isValid();

  if ($form_state['storage']['execution_successful']) {
    foreach ($output as $key => $data) {
      $dumper->setInput('data', $data);
      $dump = $dumper->output()->at('plaintext')->data();
      $form_state['storage']['output'][$key] = array(
        'dump' => '<pre>' . check_plain($dump) . '</pre>',
        'label' => $pipelineOperation->outputProperty($key, 'label'),
      );
    }
  }
  else {
    $form_state['storage']['error_message'] = t('Pipeline execution did not finish successfully - aborted in operation "%operation" (%operationId): !message', array(
      '%operation' => $pipeline->operationProperty(
        $pipelineOperation->failedOperationId(), 'label'
      ),
      '%operationId' => $pipelineOperation->failedOperationId(),
      '!message' => '<br/><pre>' . check_plain($output->errorMessage()) . '</pre>',
    ));
  }
}


/**
 * Form callback for 'admin/build/transformations/%transformations_ui_persisted_pipeline/unlock':
 * Provide a confirm form to break a pipeline's edit lock.
 */
function transformations_ui_pipeline_unlock_confirm(&$form_state, TfPipeline $pipeline) {
  $form = array();
  $other_user = ctools_object_cache_test('transformations_ui_pipeline',
    transformations_ui_pipeline_persistence_id($pipeline)
  );

  if (empty($other_user)) {
    $form['nolock'] = array(
      '#type' => 'markup',
      '#value' => t('There is no lock to break on the pipeline %name.', array(
        '%name' => $pipeline->name(),
      )),
    );
    return $form;
  }

  $cancel_path = empty($_REQUEST['cancel'])
    ? transformations_ui_pipeline_path($pipeline)
    : $_REQUEST['cancel'];

  $account = user_load($other_user->uid);
  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);

  return confirm_form($form,
    t('Are you sure you want to break the lock on pipeline %name?', array(
      '%name' => $pipeline->name()
    )),
    $cancel_path,
    t('By breaking this lock, any unsaved changes made by !user will be lost!', array('!user' => theme('username', $account))),
    t('Break lock'), t('Cancel')
  );
}

/**
 * Submit handler for the pipeline edit lock confirm form.
 */
function transformations_ui_pipeline_unlock_confirm_submit($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  transformations_ui_pipeline_discard($pipeline);
  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);
  drupal_set_message(t('The lock has been broken and you may now edit this pipeline.'));
}
