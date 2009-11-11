<?php
// $Id: iframe.views.inc,v 1.1 2008/11/09 22:13:30 neffets Exp $

/**
 * Implementation of hook_views_handlers().
 */
function iframe_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'iframe') . '/views',
    ),
    'handlers' => array(
      'iframe_views_handler_argument_target' => array(
        'parent' => 'views_handler_argument',
      ),
      'iframe_views_handler_filter_protocol' => array(
        'parent' => 'views_handler_filter_string',
      ),
    ),
  );
}

/**
 * Return CCK Views data for the iframe_field_settings($op == 'views data').
 */
function iframe_views_content_field_data($field) {
  // Build the automatic views data provided for us by CCK.
  // This creates all the information necessary for the "url" field.
  $data = content_views_field_views_data($field);

  $db_info = content_database_info($field);
  $table_alias = content_views_tablename($field);
  $field_types = _content_field_types();

  // Tweak the automatic views data for the iframe "url" field.
  // Set the filter title to "@label IFRAME-URL"
  $data[$table_alias][$field['field_name'] .'_url']['filter']['title'] = t('@label IFRAME-URL', array('@label' => t($field_types[$field['type']]['label']))) .': '. t($field['widget']['label']);
  // Remove the argument handling for URLs.
  unset($data[$table_alias][$field['field_name'] .'_url']['argument']);

  // Build out additional views data for the iframe "title" field.
  $data[$table_alias][$field['field_name'] .'_title'] = array(
    'group' => t('Content'),
    'title' => t('@label title', array('@label' => t($field_types[$field['type']]['label']))) .': '. t($field['widget']['label']) . ' ('. $field['field_name'] .')',
    'help' =>  $data[$table_alias][$field['field_name'] .'_url']['help'],
    'argument' => array(
      'field' => $db_info['columns']['title']['column'],
      'tablename' => $db_info['table'],
      'handler' => 'content_handler_argument_string',
      'click sortable' => TRUE,
      'name field' => '', // TODO, mimic content.views.inc :)
      'content_field_name' => $field['field_name'],
      'allow_empty' => TRUE,
    ),
    'filter' => array(
      'field' => $db_info['columns']['title']['column'],
      'title' => t('@label title', array('@label' => t($field_types[$field['type']]['label']))),
      'tablename' => $db_info['table'],
      'handler' => 'content_handler_filter_string',
      'additional fields' => array(),
      'content_field_name' => $field['field_name'],
      'allow_empty' => TRUE,
    ),
    'sort' => array(
      'field' => $db_info['columns']['title']['column'],
      'tablename' => $db_info['table'],
      'handler' => 'content_handler_sort',
      'content_field_name' => $field['field_name'],
      'allow_empty' => TRUE,
    ),
  );

  // Build out additional Views filter for the iframe-url "protocol" pseudo field.
  // TODO: Add a protocol argument.
  $data[$table_alias][$field['field_name'] .'_protocol'] = array(
    'group' => t('Content'),
    'title' => t('@label protocol', array('@label' => t($field_types[$field['type']]['label']))) .': '. t($field['widget']['label']) . ' ('. $field['field_name'] .')',
    'help' =>  $data[$table_alias][$field['field_name'] .'_url']['help'],
    'filter' => array(
      'field' => $db_info['columns']['url']['column'],
      'title' => t('@label protocol', array('@label' => t($field_types[$field['type']]['label']))),
      'tablename' => $db_info['table'],
      'handler' => 'iframe_views_handler_filter_protocol',
      'additional fields' => array(),
      'content_field_name' => $field['field_name'],
      'allow_empty' => TRUE,
    ),
  );

  // Build out additional Views argument for the iframe "target" pseudo field.
  // TODO: Add a target filter.
  $data[$table_alias][$field['field_name'] .'_target'] = array(
    'group' => t('Content'),
    'title' => t('@label target', array('@label' => t($field_types[$field['type']]['label']))) .': '. t($field['widget']['label']) . ' ('. $field['field_name'] .')',
    'help' =>  $data[$table_alias][$field['field_name'] .'_url']['help'],
    'argument' => array(
      'field' => $db_info['columns']['attributes']['column'],
      'title' => t('@label target', array('@label' => t($field_types[$field['type']]['label']))) .': '. t($field['widget']['label']) . ' ('. $field['field_name'] .')',
      'tablename' => $db_info['table'],
      'handler' => 'iframe_views_handler_argument_target',
      'additional fields' => array(),
      'content_field_name' => $field['field_name'],
      'allow_empty' => TRUE,
    ),
  );

  return $data;
}