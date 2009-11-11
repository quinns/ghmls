<?php
// $Id: transformations_ui.pipeline.edit.inc,v 1.17 2009/06/30 09:59:55 jpetso Exp $
/**
 * @file
 * Transformations UI -
 * An interface for managing transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Form callback for 'admin/build/transformations/%transformations_ui_persisted_pipeline':
 * A form to inspect & edit a pipeline.
 */
function transformations_ui_pipeline_edit(&$form_state, TfPipeline $pipeline) {
  // In order to overcome the limitations of the menu system (especially with
  // respect to local tasks which don't work well for child paths), this form
  // also acts as a hub for other forms to appear on the "Edit" tab.
  if (func_num_args() > 2) {
    $args = func_get_args();
    $page = $args[2];
    unset($args[2]);

    if (func_num_args() >= 5 && $page == 'input') {
      if (func_num_args() >= 6 && $args[3] == 'import') {
        unset($args[3]);
        $args[5] = urldecode($args[5]);
        return call_user_func_array(
          'transformations_ui_pipeline_operation_input_import', $args
        );
      }
      $args[4] = urldecode($args[4]);
      return call_user_func_array(
        'transformations_ui_pipeline_operation_input_edit', $args
      );
    }
  }

  $pipelineOperation = TfOperation::load('TfPipelineOperation');
  $pipelineOperation->setInput('pipeline', $pipeline);

  if (!$pipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation could not be initialized.'), 'error');
    return array();
  }

  drupal_add_css(drupal_get_path('module', 'transformations_ui') . '/transformations_ui.css');

  $pipelinePath = transformations_ui_pipeline_path($pipeline);

  $form = array();
  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);

  $blocks = array();

  $isEditingOperation = isset($_GET['edit-op']);
  $isEditingOperationInput = $isEditingOperation && $_GET['action'] == 'input';
  $isEditingOperationOutput = $isEditingOperation && $_GET['action'] == 'output';
  $isRenaming = $isEditingOperation && $_GET['action'] == 'rename';

  $isConnectingTo = array(
    'input' => ($isEditingOperation && $_GET['action'] == 'input'),
    'output' => ($isEditingOperation && $_GET['action'] == 'output'),
  );
  if ($isConnectingTo['input'] || $isConnectingTo['output']) {
    $connectingKey = urldecode($_GET['key']);
  }

  // Create blocks for the regular operations, and also for
  // pipeline parameters/outputs.
  $blocks = array();
  $blocks[TfPipeline::Parameter] = array(
    'label' => t('Pipeline parameters'),
    'description' => t('The set of dynamic inputs for the pipeline. Connect "New parameter" with an operation input to create a new pipeline parameter.'),
    'input' => array('keys' => array(), 'sources' => array()),
    'output' => array(
      'keys' => array(),
      'targets' => $pipeline->targets(TfPipeline::Parameter),
    ),
    'specialEntity' => TRUE,
  );
  foreach ($blocks[TfPipeline::Parameter]['output']['targets'] as $sourceKey => $targets) {
    $blocks[TfPipeline::Parameter]['output']['keys'][] = $sourceKey;
  }
  $blocks[TfPipeline::Parameter]['output']['keys'][] = TfPipeline::NewOutboundConnection;

  $operations = array();
  $jsOperations = array();
  $jsConnections = array();
  $jsSlots = array();

  foreach ($pipeline->operationIds() as $operationId) {
    $operation = $pipelineOperation->operation($operationId);
    $operations[$operationId] = $operation;

    $blocks[$operationId] = array(
      'label' => $pipeline->operationProperty($operationId, 'label'),
      'description' => $pipeline->operationProperty($operationId, 'description'),
      'input' => array(
        'keys' => $operation->inputKeys(),
        'sources' => $pipeline->sources($operationId),
      ),
      'output' => array(
        'keys' => $operation->outputKeys(),
        'targets' => $pipeline->targets($operationId),
      ),
    );
  }

  $blocks[TfPipeline::Output] = array(
    'label' => t('Pipeline outputs'),
    'description' => t('The set of result outputs for the pipeline. These outputs will be returned to users of the pipeline, or shown on the "Execute" form when the pipeline is executed. Connect "New output" with an operation output to create a new pipeline output.'),
    'input' => array(
      'keys' => array(),
      'sources' => $pipeline->sources(TfPipeline::Output),
    ),
    'output' => array('keys' => array(), 'targets' => array()),
    'specialEntity' => TRUE,
  );
  foreach ($blocks[TfPipeline::Output]['input']['sources'] as $targetKey => $source) {
    $blocks[TfPipeline::Output]['input']['keys'][] = $targetKey;
  }
  $blocks[TfPipeline::Output]['input']['keys'][] = TfPipeline::NewOutboundConnection;

  // Big pile of confusing code that generates the blocks and connection buttons.
  foreach ($blocks as $operationId => $block) {
    $jsOperations[$operationId] = array();

    $jsPosition = $pipeline->property('transformations_ui:position:' . $operationId);
    if (!empty($jsPosition)) {
      $jsOperations[$operationId] = array_merge($jsOperations[$operationId], $jsPosition);
    }

    $operationElements[$operationId] = array(
      '#value' => '',
      '#prefix' => '<div id="' . $operationId . '" class="transformations-operation-block">'
        . '<div class="transformations-operation-block-table">',
      '#suffix' => '</div></div>',
    );
    $operationElements[$operationId]['header'] = array(
      '#value' => '',
      '#prefix' => '<div class="transformations-operation-block-header">'
        . '<div class="transformations-operation-block-header-cell">',
      '#suffix' => '</div></div>',
    );
    $operationElements[$operationId]['header']['label'] = array(
      '#value' => check_plain($block['label']),
      '#prefix' => '<div class="transformations-operation-block-header-label" title="'
        . check_plain($block['description']) . '">',
      '#suffix' => '</div>',
    );
    $operationElements[$operationId]['header']['actions'] = array(
      '#value' => '',
      '#prefix' => '<div class="transformations-operation-block-header-actions">',
      '#suffix' => '</div>',
    );

    // Operation editing functionality is only available for real operations.
    if ($isEditingOperation && $_GET['edit-op'] == $operationId) {
      $operationElements[$operationId]['header']['actions']['cancel'] = array(
        '#value' => transformations_ui_operation_action_image(
          'dialog-cancel', t('Cancel'), $pipelinePath
        ),
        '#weight' => 10,
      );
    }

    if (empty($block['specialEntity'])) {
      if ($isEditingOperation && $operationId == $_GET['edit-op']) {
        if ($isRenaming) {
          unset($operationElements[$operationId]['header']['label']['#value']);

          $operationElements[$operationId]['header']['label'] += array(
            '#type' => 'textfield',
            '#default_value' => $block['label'],
            '#size' => strlen($block['label']),
          );
          $operationElements[$operationId]['header']['actions']['update'] = array(
            '#type' => 'image_button',
            '#title' => t('Assign new name'),
            '#src' => drupal_get_path('module', 'transformations_ui') . '/images/dialog-ok.png',
            '#name' => 'remove-' . $operationId,
            '#tf_operation_id' => $operationId,
            '#submit' => array('transformations_ui_pipeline_operation_rename_submit'),
          );
        }
      }
      elseif (!isset($_GET['edit-op'])) {
        $operationElements[$operationId]['header']['actions']['rename'] = array(
          '#value' => transformations_ui_operation_action_image(
            'edit-rename', t('Rename operation'), $pipelinePath, array(
              'query' => 'edit-op=' . drupal_urlencode($operationId) . '&action=rename',
            )
          ),
        );
        $operationElements[$operationId]['header']['actions']['remove'] = array(
          '#value' => transformations_ui_operation_action_image(
            'edit-delete', t('Remove operation from pipeline'),
            $pipelinePath . '/remove-operation/' . $operationId
          ),
        );
      }
    }

    $i = 0;
    $max_actions_count = is_string($operationId) ? 2 : 1;
    while (count(element_children($operationElements[$operationId]['header']['actions'])) < $max_actions_count) {
      ++$i;
      // Have a non-empty value so that the form element is rendered at all.
      $operationElements[$operationId]['header']['actions']['pad' . $i] = array(
        '#value' => transformations_ui_operation_action_image('blank', ''),
        '#weight' => -100,
      );
    }

    $operationElements[$operationId]['connections'] = array(
      '#value' => '',
      '#prefix' => '<div class="transformations-operation-block-connections">'
        . '<div class="transformations-operation-block-connections-table">'
        . '<div class="transformations-operation-block-connections-body">',
      '#suffix' => '</div></div></div>',
    );
    $operationElements[$operationId]['connections']['input'] = array(
      '#type' => 'markup',
      '#value' => '',
      '#prefix' => '<div class="transformations-operation-block-inputs">',
      '#suffix' => '</div>',
    );
    $operationElements[$operationId]['connections']['output'] = array(
      '#type' => 'markup',
      '#value' => '',
      '#prefix' => '<div class="transformations-operation-block-outputs">',
      '#suffix' => '</div>',
    );

    // Construct input/output connection elements, as form buttons.
    foreach (array('input', 'output') as $keyType) {
      $otherKeyType = ($keyType == 'input') ? 'output' : 'input';

      foreach ($block[$keyType]['keys'] as $key) {
        $jsCurrentConnections = array();
        $classes = array();
        $styles = array();

        // Some more explanatory variables for the key's connection state.
        $isKeyConnecting = $isConnectingTo[$keyType] &&
          $operationId == $_GET['edit-op'] && $key == $connectingKey;

        $isConnectingCurrentPartner = FALSE;

        // Some key type specific differences.
        if ($keyType == 'input') { // a.k.a. "connection target"
          $source = $pipeline->source($operationId, $key);
          $isKeyConnected = !empty($source);

          if ($isKeyConnected && isset($source['key'])) {
            $sourceEntity = $source['entity'];
            $sourceKey = $source['key'];
            $jsCurrentConnections[] = array(
              'keyType' => 'output',
              'entity' => $sourceEntity,
              'key' => $sourceKey,
            );

            if ($isConnectingTo[$otherKeyType]
                && $_GET['edit-op'] == $sourceEntity && $connectingKey == $sourceKey) {
              $isConnectingCurrentPartner = TRUE;
            }
          }
        }
        elseif ($keyType == 'output') { // a.k.a. "connection source"
          $targets = $pipeline->targets($operationId, $key);
          $isKeyConnected = !empty($targets);

          $sourceEntity = $operationId;
          $sourceKey = $key;

          foreach ($targets as $otherOperationId => $targetInfo) {
            foreach ($targetInfo as $otherKey => $true) {
            if ($isConnectingTo[$otherKeyType]) {
                if ($_GET['edit-op'] == $otherOperationId && $connectingKey == $otherKey) {
                  $isConnectingCurrentPartner = TRUE;
                }
              }
              $jsCurrentConnections[] = array(
                'keyType' => 'input',
                'entity' => $otherOperationId,
                'key' => $otherKey,
              );
            }
          }
        }

        $elementId = $keyType . '-' . $operationId . '-' . $key;
        if (!empty($jsCurrentConnections)) {
          $jsConnections[$elementId] = $jsCurrentConnections;
        }
        unset($label);
        unset($description);

        // JavaScript settings for use by the drag&drop behavior.
        $jsSlots[$elementId] = array(
          'keyType' => $keyType,
          'entity' => $operationId,
          'key' => $key,
        );

        // Determine how to name the label. The thing that makes this a bit
        // more complicated is that we want to name the pipeline parameters and
        // pipeline outputs similar to the connected operation slots, and
        // we also need to special-case the virtual "New parameter/output" key.
        if ($operationId == TfPipeline::Parameter) {
          if ($key == TfPipeline::NewOutboundConnection) {
            $label = t('New parameter');
          }
          else {
            foreach ($targets as $targetEntity => $targetInfo) {
              if (isset($operations[$targetEntity])) {
                foreach ($targetInfo as $targetKey => $true) {
                  try {
                    $label = $operations[$targetEntity]->inputProperty($targetKey, 'label');
                  }
                  catch (Exception $e) {
                    $label = t('Orphaned connection: "@key"', array(
                      '@key' => $targetKey,
                    ));
                    $description = t('This parameter has previously been connected, but does not exist in the operation itself. Unless it is disconnected, the pipeline will fail to execute successfully.');
                  }
                }
              }
            }
          }
        }
        elseif ($operationId == TfPipeline::Output) {
          if ($key == TfPipeline::NewOutboundConnection) {
            $label = t('New output');
          }
          elseif (isset($operations[$sourceEntity])) {
            try {
              $label = $operations[$sourceEntity]->outputProperty($sourceKey, 'label');
            }
            catch (Exception $e) {
              $label = t('Orphaned connection: "@key"', array(
                '@key' => $sourceKey,
              ));
              $description = t('This output has previously been connected, but does not exist in the operation itself. Unless it is disconnected, the pipeline will fail to execute successfully.');
            }
          }
        }
        elseif (isset($operations[$operationId])) {
          try {
            $keyProperty = ($keyType == 'input') ? 'inputProperty' : 'outputProperty';
            $label = $operations[$operationId]->$keyProperty($key, 'label');
            $description = $operations[$operationId]->$keyProperty($key, 'description');

            if ($keyType == 'input' && !$operations[$operationId]->inputProperty($key, 'required')) {
              $description = empty($description)
                ? t('(optional)')
                : t('(optional) !description', array('!description' => $description));
            }
          }
          catch (Exception $e) {
            $label = t('Orphaned connection: "@key"', array(
              '@key' => $key,
            ));
            $description = ($keyType == 'input')
              ? t('This input has previously been connected, but does not exist in the operation itself (which means that the operation has changed). Unless it is disconnected, the pipeline will fail to execute successfully.')
              : t('This output has previously been connected, but does not exist in the operation itself (which means that the operation has changed). Unless it is disconnected, the pipeline will fail to execute successfully.');
          }
        }
        if (!isset($label)) {
          $label = $key;
        }
        if (!isset($description)) {
          $description = '';
        }

        // Determine whether this slot can be modified right now.
        $disabled = $isEditingOperation &&
          (!$isConnectingTo[$otherKeyType] || $_GET['edit-op'] == $operationId);

        if ($operationId == TfPipeline::Parameter &&
            $isConnectingTo[$otherKeyType] && $_GET['edit-op'] == TfPipeline::Output) {
          $disabled = TRUE;
        }
        elseif ($operationId == TfPipeline::Output &&
            $isConnectingTo[$otherKeyType] && $_GET['edit-op'] == TfPipeline::Parameter) {
          $disabled = TRUE;
        }
        if ($isKeyConnecting && $isKeyConnected) {
          $disabled = FALSE;
        }

        // The label and connector button for the input/output slot.
        $element = array(
          '#type' => 'markup',
          '#value' => '',
          '#prefix' => '<div id="' . $elementId .
            '" class="transformations-operation-slot transformations-operation-'
            . $keyType . '"><div class="transformations-drop-target">',
          '#suffix' => '</div></div>',
        );
        $element['label'] = array(
          '#type' => 'markup',
          '#value' => '<span class="transformations-operation-slot-label '
            . 'transformations-operation-' . $keyType . '-label">'
            . check_plain($label) . '</span>',
          '#weight' => 0,
        );
        if (!empty($description)) {
          $element['label']['#value'] =
            '<span title="' . check_plain($description) . '">'
            . $element['label']['#value'] . '</span>';
        }

        if ($disabled) {
          $connectorImage = transformations_ui_operation_action_image(
            'connector-disabled', $description
          );
        }
        else {
          if ($isKeyConnected && $isKeyConnecting) {
            $connectorImage = transformations_ui_operation_action_image(
              'edit-delete', t('Click to disconnect'),
              'transformations/slot-disconnect/' . $form['#pipeline_id']
              . '/' . drupal_urlencode($operationId) . '/' . $keyType
              . '/' . drupal_urlencode($key)
            );
          }
          elseif ($isConnectingTo[$otherKeyType]) {
            if ($keyType == 'output') { // this key: source, other key: target
              $args = array(
                drupal_urlencode($operationId), drupal_urlencode($key),
                drupal_urlencode($_GET['edit-op']), drupal_urlencode($_GET['key']),
              );
            }
            elseif ($keyType == 'input') { // other key: source, this key: target
              $args = array(
                drupal_urlencode($_GET['edit-op']), drupal_urlencode($_GET['key']),
                drupal_urlencode($operationId), drupal_urlencode($key),
              );
            }
            $connectorImage = transformations_ui_operation_action_image(
              'connector-enabled', t('Click to connect'),
              'transformations/slot-connect/' . $form['#pipeline_id'] . '/' . implode('/', $args)
            );
          }
          else {
            $connectorImage = transformations_ui_operation_action_image(
              'connector-enabled', $description,
              transformations_ui_pipeline_path($pipeline), array('query' => array(
                'edit-op' => drupal_urlencode($operationId),
                'action' => $keyType,
                'key' => drupal_urlencode($key),
              ))
            );
          }
        }
        $element['connect'] = array(
          '#type' => 'markup',
          '#value' => $connectorImage,
          '#weight' => -5,
        );

        // And now, a gazillion customizations depending on the current status
        // of the input/output slot and its connection.

        if ($keyType == 'input' && (!$isEditingOperation || $isKeyConnecting)) {
          if (!$disabled && $operationId != TfPipeline::Output) {
            $editInputPath = $pipelinePath . '/edit/input/'
              . drupal_urlencode($operationId) . '/' . drupal_urlencode($key);
            $element['label']['#value'] = l($element['label']['#value'],
              $editInputPath, array('html' => TRUE)
            );
          }
        }

        if ($isKeyConnected) {
          $classes[] = 'connected';
        }
        if ($isKeyConnecting) {
          $classes[] = 'connecting';
        }
        if ($isConnectingTo[$otherKeyType] || $isKeyConnecting) {
          $classes[] = 'potential-connection-target';
        }

        unset($bgColor);
        if ($isKeyConnecting) {
          $bgColor = '#ffcc00';
        }
        elseif ($isConnectingCurrentPartner) {
          $bgColor = '#ffee99';
        }
        elseif ($isKeyConnected) {
          $bgColor = transformations_ui_connection_css_background_color(
            $sourceEntity, $sourceKey, $disabled
          );
        }
        if (isset($bgColor)) {
          $styles[] = "background-color: $bgColor";
        }

        $classes[] = $disabled ? 'disabled' : 'enabled';

        $element_attributes = array(
          'class="transformations-operation-slot-connector ' . implode(' ', $classes) .'"',
        );
        if (!empty($styles)) {
          $element_attributes[] = 'style="' . implode('; ', $styles) .'"';
        }
        $element['connect']['#prefix'] = '<span ' . implode(' ', $element_attributes) . '">';
        $element['connect']['#suffix'] = '</span>';

        $operationElements[$operationId]['connections'][$keyType][$elementId] = $element;
      } // end of input/output element construction
    } // end of foreach (array('input', 'output'))
  }


  // Add JavaScript and connection info enabling it to highlight opposite keys.
  drupal_add_js(drupal_get_path('module', 'transformations_ui') . '/transformations_ui.js');
  drupal_add_js(array('transformationsUiConnections' => $jsConnections), 'setting');

  if (module_exists('jquery_ui')
      //&& strpos(jquery_update_get_version(), '1.3') === 0
      //&& strpos(jquery_ui_get_version(), '1.7') === 0
  ) {
    jquery_ui_add(array('ui.resizable', 'ui.draggable', 'ui.droppable'));

    drupal_add_js(array('transformationsUiOperations' => $jsOperations), 'setting');
    drupal_add_js(array('transformationsUiSlots' => $jsSlots), 'setting');
    drupal_add_js(array('transformationsUiPipelinePersistenceId' => $form['#pipeline_id']), 'setting');

    drupal_add_js(drupal_get_path('module', 'transformations_ui') . '/transformations_ui.dragndrop.js');
  }

  if (empty($operationElements)) {
    $form['operations'] = array(
      '#value' => t('No operations have yet been defined for this pipeline.'),
    );
  }
  else {
    $form['operations'] = array(
      '#value' => '',
      '#prefix' => '<div class="transformations-pipeline-canvas"><div class="transformations-operation-blocks clear-block">',
      '#suffix' => '</div><div class="ui-resizable-handle" id="transformations-grippie">v</div></div>',
    );
    $form['operations'] += $operationElements;
  }

  if (!transformations_ui_is_pipeline_locked($pipeline) && $pipeline->isModified()) {
    $form['save'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#submit' => array('transformations_ui_pipeline_edit_save'),
      '#disabled' => $isEditingOperation,
    );
    $form['discard'] = array(
      '#type' => 'submit',
      '#value' => t('Discard changes'),
      '#submit' => array('transformations_ui_pipeline_edit_discard'),
      '#disabled' => $isEditingOperation,
    );
  }
  else {
    $form['cancel'] = array(
      '#type' => 'submit',
      '#value' => t('Done'),
      '#submit' => array('transformations_ui_goto_submit'),
      '#goto_path' => 'admin/build/transformations',
      '#disabled' => $isEditingOperation,
    );
  }

  if (!transformations_ui_is_pipeline_locked($pipeline) && $pipeline->id()) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#submit' => array('transformations_ui_pipeline_edit_delete'),
      '#disabled' => $isEditingOperation,
    );
  }

  return $form;
}

/**
 * Submit callback for a pipeline's "Save" button.
 */
function transformations_ui_pipeline_edit_save($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);

  if (!$pipeline) {
    drupal_set_message(t('Error loading the pipeline.'), 'error');
    return;
  }
  transformations_ui_pipeline_save($pipeline);
  drupal_set_message(t('%pipeline has been saved.', array(
    '%pipeline' => $pipeline->name(),
  )));
  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);
}

/**
 * Submit callback for a pipeline's "Discard changes" button.
 */
function transformations_ui_pipeline_edit_discard($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);

  if (!$pipeline) {
    drupal_set_message(t('Error loading the pipeline.'), 'error');
    return;
  }
  transformations_ui_pipeline_discard($pipeline);

  if ($pipeline->id()) {
    drupal_set_message(t('%pipeline has restored to its saved state.', array(
      '%pipeline' => $pipeline->name(),
    )));
  }
  else {
    drupal_set_message(t('%pipeline has been discarded.', array(
      '%pipeline' => $pipeline->name(),
    )));
    $form_state['redirect'] = 'admin/build/transformations';
  }
}

/**
 * Submit callback for a pipeline's "Delete" button.
 */
function transformations_ui_pipeline_edit_delete($form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline) . '/delete';
}

/**
 * Return markup for an icon that is supposed to be inserted in the
 * 'actions' div of an operation block header.
 */
function transformations_ui_operation_action_image($icon, $title, $path = NULL, $l_options = array()) {
  $image = theme('image',
    drupal_get_path('module', 'transformations_ui') . "/images/$icon.png",
    $title, $title
  );
  if (!isset($path)) {
    return $image;
  }
  return l($image, $path, array('html' => TRUE) + $l_options);
}

/**
 * Submit handler for the "Rename" ("ok") button of an operation.
 */
function transformations_ui_pipeline_operation_rename_submit($form, &$form_state) {
  $operationId = $form_state['clicked_button']['#tf_operation_id'];
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  $pipeline->setOperationProperty($operationId, 'label', $form_state['values']['label']);
  transformations_ui_pipeline_persist($pipeline);
}

/**
 * Page callback for
 * 'transformations/slot-connect/$pipeline/$sourceEntity/$sourceKey/$targetEntity/$targetKey'.
 */
function transformations_ui_pipeline_operation_slot_connect(TfPipeline $pipeline, $sourceEntity, $sourceKey, $targetEntity, $targetKey) {
  $sourceKey = urldecode($sourceKey);
  $targetKey = urldecode($targetKey);

  $pipeline->connect(
    $sourceEntity, $sourceKey, $targetEntity, $targetKey
  );
  transformations_ui_pipeline_persist($pipeline);
  transformations_ui_pipeline_edit_redirect($pipeline);
  return '';
}

/**
 * Page callback for 'transformations/slot-disconnect/$pipeline/$entity/$keyType/$key'.
 */
function transformations_ui_pipeline_operation_slot_disconnect(TfPipeline $pipeline, $entity, $keyType, $key) {
  $key = urldecode($key);

  if ($keyType == 'output') {
    $pipeline->disconnectSource($entity, $key);
    transformations_ui_pipeline_persist($pipeline);
  }
  elseif ($keyType == 'input') {
    $pipeline->disconnectTarget($entity, $key);
    transformations_ui_pipeline_persist($pipeline);
  }
  else {
    transformations_ui_pipeline_edit_redirect($pipeline,
      t('Unrecognized key type (@key-type).', array('@key-type' => $keyType))
    );
  }
  transformations_ui_pipeline_edit_redirect($pipeline);
  return '';
}

function transformations_ui_pipeline_edit_redirect(TfPipeline $pipeline, $errorMessage = NULL) {
  if (!empty($_REQUEST['ajax'])) {
    print drupal_to_js(empty($errorMessage) ? TRUE : FALSE);
  }
  else {
    if (!empty($errorMessage)) {
      drupal_set_message($errorMessage);
    }
    drupal_goto(transformations_ui_pipeline_path($pipeline));
  }
}

/**
 * Page callback for the "Remove" button on each operation.
 */
function transformations_ui_pipeline_operation_remove(TfPipeline $pipeline, $operationId) {
  $pipeline->removeOperation($operationId);
  transformations_ui_pipeline_persist($pipeline);
  drupal_goto(transformations_ui_pipeline_path($pipeline));
}

/**
 * JavaScript callback for 'transformations/ajax/position-save':
 * Save the position of an operation block. (There's no getter function for
 * the block position as it's delivered to the script via Drupal.settings.)
 */
function transformations_ui_pipeline_operation_position_save($pipeline, $entity, $zIndex, $top, $left) {
  if (empty($entity) || !is_numeric($zIndex) || !is_numeric($top) || !is_numeric($left)) {
    print drupal_to_js(FALSE);
    return;
  }
  $pipeline->setProperty('transformations_ui:position:' . $entity, array(
    'z-index' => $zIndex,
    'top' => max($top, 0),
    'left' => max($left, 0),
  ));
  transformations_ui_pipeline_persist($pipeline);
  print drupal_to_js(TRUE);
}

/**
 * Calculate a color for a given @p $entity and @p $key of a connection.
 * Preferably, you would always use the values of the connection source,
 * because each source can be assigned to multiple targets but a target can
 * only be assigned to a single source. (In other words, by using the source
 * entity and key, you get the same color for the same data.)
 *
 * @return
 *   An array with two elements, the foreground and background values. Assign
 *   them with @code list($bgColor, $fgColor) = (...function call...) @endcode.
 */
function transformations_ui_connection_css_background_color($entity, $key, $disabled = FALSE) {
  $r = 192 + (abs(crc32(md5($entity . '::' . $key))) % 48);
  $g = 192 + (abs(crc32(md5($key))) % 50);
  $b = 192 + (abs(crc32(md5($entity))) % 64);

  if (abs($g - $r) < 10) {
    $r -= 10;
    $g = min($g + 20, 255);
  }
  if (abs($b - $g) < 10) {
    $g -= 10;
    $b = min($b + 20, 255);
  }

  if ($disabled) {
    $r = min($r + 20, 255);
    $g = min($g + 20, 255);
    $b = min($b + 20, 255);
  }
  return "rgb($r, $g, $b)";
}


/**
 * Return a form element with links to the various methods to set input data.
 */
function transformations_ui_pipeline_operation_input_pseudotabs($current_form_name, TfPipelineOperation $pipelineOperation, $operationId, $inputKey) {
  $path_base = transformations_ui_pipeline_path($pipelineOperation->pipeline()) . '/input';
  $path_suffix = '/' . drupal_urlencode($operationId) . '/' . drupal_urlencode($inputKey);
  $edit_path = $path_base . $path_suffix;
  $import_path = $path_base . '/import' . $path_suffix;
  $links = array();

  $links[] = ($current_form_name == 'edit')
    ? '<strong>' . t('Edit value') . '</strong>'
    : l(t('Edit value'), $edit_path);

  $links[] = ($current_form_name == 'import')
    ? '<strong>' . t('Import value from pipeline') . '</strong>'
    : l(t('Import value from pipeline'), $import_path);

  $operation = $pipelineOperation->operation($operationId);

  $form = array(
    '#type' => 'fieldset',
    '#title' => t(
      'Input "@input-name" in operation "@operation-name"', array(
        '@input-name' => $operation->inputProperty($inputKey, 'label'),
        '@operation-name' => $pipelineOperation->pipeline()->operationProperty($operationId, 'label'),
    )),
  );
  $form['links'] = array(
    '#type' => 'markup',
    '#value' => implode(' | ', $links),
  );
  return $form;
}

/**
 * Form callback for assigning fixed data to an operation input, at
 * 'admin/build/transformations/%transformations_ui_persisted_pipeline/edit/input/%/%':
 */
function transformations_ui_pipeline_operation_input_edit(&$form_state, TfPipeline $pipeline, $operationId, $inputKey) {
  $pipelineOperation = TfOperation::load('TfPipelineOperation');
  $pipelineOperation->setInput('pipeline', $pipeline);

  if (!$pipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation could not be initialized.'), 'error');
    return array();
  }
  $operation = $pipelineOperation->operation($operationId);

  $form_element = TfDataWidget::forInput($operation, $inputKey);
  if (!$form_element) {
    drupal_set_message(t('No input with key %input-key exists for this operation.', array('%input-key' => $inputKey)), 'error');
    return array();
  }

  $form = array();
  $locked = transformations_ui_is_pipeline_locked($pipeline);

  // If there is an existing source connection, warn the user.
  $source = $pipeline->source($operationId, $inputKey);
  if ($source && isset($source['key']) && !$locked) {
    if ($source['entity'] == TfPipeline::Parameter) {
      $message = t('This input is already connected to a pipeline parameter. If you assign a new value, the input will be disconnected from that parameter. !click-here to go back without making changes.', array(
        '!click-here' => l(t('Click here'), transformations_ui_pipeline_path($pipeline)),
      ));
    }
    else {
      $sourceOperation = $pipelineOperation->operation($source['entity']);
      $message = t('This input is already connected to the @output-key output of the operation "@operation-name". If you assign a new value, the input will be disconnected from that operation output. !click-here to go back without making changes.', array(
        '@output-key' => $sourceOperation->outputProperty($source['key'], 'label'),
        '@operation-name' => $pipeline->operationProperty($source['entity'], 'label'),
        '!click-here' => l(t('Click here'), transformations_ui_pipeline_path($pipeline)),
      ));
    }
    $form['warning'] = array(
      '#value' => $message,
      '#prefix' => '<div class="messages warning">',
      '#suffix' => '</div>',
    );
  }

  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);
  $form['#operation_id'] = $operationId;
  $form['#input_key'] = $inputKey;

  // Tab replacement (proper menu items are futile at this point).
  $form['tabs'] = transformations_ui_pipeline_operation_input_pseudotabs(
    'edit', $pipelineOperation, $operationId, $inputKey
  );

  // The input element for directly editing the value.
  $form['input'] = $form_element;

  // Buttons.
  $form['actions'] = array(
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
  );
  if (!$locked) {
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Store value'),
      '#submit' => array('transformations_ui_pipeline_operation_input_edit_submit'),
    );
  }
  $form['actions']['cancel'] = array(
    '#type' => 'markup',
    '#value' => l($locked ? t('Back') : t('Cancel'),
      transformations_ui_pipeline_path($pipeline)
    ),
  );

  return $form;
}

/**
 * Submit callback for the "Assign fixed input data" form.
 */
function transformations_ui_pipeline_operation_input_edit_submit(&$form, &$form_state) {
  $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
  $operationId = $form['#operation_id'];
  $inputKey = $form['#input_key'];
  $widgetValue = $form_state['values']['input'];

  if ($widgetValue->useDefault()) {
    $pipeline->disconnectTarget($operationId, $inputKey);
  }
  else {
    $pipeline->connect(TfPipeline::Data, $widgetValue->value(), $operationId, $inputKey);
  }
  transformations_ui_pipeline_persist($pipeline);
  $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);
}

/**
 * Form callback for assigning fixed data to an operation input, at
 * 'admin/build/transformations/%transformations_ui_persisted_pipeline/edit/input/import/%/%':
 */
function transformations_ui_pipeline_operation_input_import(&$form_state, TfPipeline $pipeline, $operationId, $inputKey) {
  $pipelineOperation = TfOperation::load('TfPipelineOperation');
  $pipelineOperation->setInput('pipeline', $pipeline);

  if (!$pipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation could not be initialized.'), 'error');
    return array();
  }
  $operation = $pipelineOperation->operation($operationId);

  $form['#pipeline_id'] = transformations_ui_pipeline_persistence_id($pipeline);
  $form['#operation_id'] = $operationId;
  $form['#input_key'] = $inputKey;

  // Tab replacement (proper menu items are futile at this point).
  $form['tabs'] = transformations_ui_pipeline_operation_input_pseudotabs(
    'import', $pipelineOperation, $operationId, $inputKey
  );

  if (!isset($form_state['storage']['import_pipeline_id'])) {
    $form['pipeline'] = array(
      '#type' => 'select',
      '#title' => t('Pipeline to import data from'),
      '#description' => t('If another pipeline should provide the data, you can use this functionality to (pre-)execute that pipeline and store one of its output results as new value for this input.'),
      '#options' => transformations_ui_pipeline_options(),
    );
  }
  else {
    $form['#import_pipeline_id'] = $form_state['storage']['import_pipeline_id'];

    $importPipelineOperation = TfOperation::load('TfPipelineOperation');
    $importPipelineOperation->setInput('pipeline', $form_state['storage']['import_pipeline_id']);

    if (!$importPipelineOperation->isPipelineValid()) {
      drupal_set_message(t('Pipeline operation for data import could not be initialized.'), 'error');
      return array();
    }
    $importPipeline = $importPipelineOperation->pipeline();
    $form['#import_pipeline_id'] = $importPipeline->id();

    $form['transformations_ui_import_pipeline_output'] = array(
      '#type' => 'select',
      '#title' => t('Pipeline output to import'),
      '#description' => t('Select the pipeline output whose result value will be assigned to this operation input.'),
    );
    foreach ($importPipelineOperation->outputKeys() as $key) {
      $form['transformations_ui_import_pipeline_output']['#options'][$key] =
        check_plain($importPipelineOperation->outputProperty($key, 'label'));
    }

    if (count($importPipeline->targets(TfPipeline::Parameter))) {
      $form['input'] = array(
        '#type' => 'fieldset',
        '#title' => t('Enter pipeline parameters'),
        '#description' => t('Enter all required pipeline parameters here, and press the "Import value" button to run the pipeline. You can choose to leave out non-required parameters, a default value will be used for those if no value is given. After successful pipeline execution, the value will be assigned to this operation input.'),
      );
      foreach ($importPipelineOperation->inputKeys() as $key) {
        if ($key == 'pipeline') {
          continue; // Filled out internally, see above.
        }
        $form['input'][$key] = TfDataWidget::forInput($importPipelineOperation, $key);
      }
    }
  }

  // Buttons.
  $locked = transformations_ui_is_pipeline_locked($pipeline);

  $form['actions'] = array(
    '#prefix' => '<div class="container-inline">',
    '#suffix' => '</div>',
  );
  if (!$locked) {
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => isset($form_state['storage']['import_pipeline_id'])
        ? t('Import value')
        : t('Select pipeline'),
      '#submit' => array('transformations_ui_pipeline_operation_input_import_submit'),
    );
  }
  if (isset($form_state['storage']['import_pipeline_id'])) {
    $form['actions']['back'] = array(
      '#type' => 'markup',
      '#value' => l(t('Select another pipeline'),
        transformations_ui_pipeline_path($pipeline) . '/input/import/'
        . $operationId . '/' . $inputKey
      ) . ' | ',
    );
  }
  $form['actions']['cancel'] = array(
    '#type' => 'markup',
    '#value' => l($locked ? t('Back') : t('Cancel'),
      transformations_ui_pipeline_path($pipeline)
    ),
  );
  return $form;
}

/**
 * Submit callback for the "Import input value from pipeline" form.
 */
function transformations_ui_pipeline_operation_input_import_submit($form, &$form_state) {
  // If the user has just selected the data import pipeline, go back to the form
  // and let her input parameters as well as select the target pipeline output.
  if (isset($form_state['values']['pipeline'])) {
    $form_state['storage']['import_pipeline_id'] = $form_state['values']['pipeline'];
    return;
  }
  $form_state['storage']['import_pipeline_id'] = $form['#import_pipeline_id'];

  // Load the data import pipeline that the user has specified.
  $importPipelineOperation = TfOperation::load('TfPipelineOperation');
  $importPipelineOperation->setInput('pipeline', $form_state['storage']['import_pipeline_id']);

  if (!$importPipelineOperation->isPipelineValid()) {
    drupal_set_message(t('Pipeline operation for data import could not be initialized.'), 'error');
    return;
  }

  // Assign input values, a.k.a. pipeline parameters.
  foreach ($importPipelineOperation->inputKeys() as $key) {
    if ($key == 'pipeline') {
      continue; // Filled out internally, see above.
    }
    $widgetValue = $form_state['values'][$key];

    if (!$widgetValue->useDefault()) {
      $data = $widgetValue->value();
      $importPipelineOperation->setInput($key, $data);
    }
  }

  $output = $importPipelineOperation->output();

  if ($output->isValid()) {
    $outputKey = $form_state['values']['transformations_ui_import_pipeline_output'];
    $pipeline = transformations_ui_persisted_pipeline_load($form['#pipeline_id']);
    $pipeline->connect(
      TfPipeline::Data, $output->at($outputKey),
      $form['#operation_id'], $form['#input_key']
    );

    transformations_ui_pipeline_persist($pipeline);
    unset($form_state['storage']);
    $form_state['redirect'] = transformations_ui_pipeline_path($pipeline);

    drupal_set_message(t('The pipeline result has been stored as new input value.'));
  }
  else {
    drupal_set_message(t('Pipeline execution did not finish successfully, no input value has been imported. The pipeline aborted in operation "%operation" (%operationId): !message', array(
      '%operation' => $importPipelineOperation->pipeline()->operationProperty(
        $importPipelineOperation->failedOperationId(), 'label'
      ),
      '%operationId' => $importPipelineOperation->failedOperationId(),
      '!message' => '<br/><pre>' . check_plain($output->errorMessage()) . '</pre><br/>',
    )), 'error');
  }
}
