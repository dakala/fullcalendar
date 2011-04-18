<?php

/**
 * @file
 * Color page callbacks for the Fullcalendar colors module.
 */

/**
 * General configuration form for setting colors for Fullcalendar events.
 *
 * @see - http://drupal.org/node/1041068
 * @see - patch for fullcalendar.module at http://drupal.org/node/1041068#comment-4196492
 */
function fullcalendar_admin_color_node_type_settings() {

  $form = _load_colorpicker();

  /**
   * Node types
   *
   * @todo - Allow user to enable/disable node type color selection.
   * @todo - Allow user to set colors per node type.
   */
  $form['node_type_colors'] = array(
    '#type' => 'item',
    '#title' => t('Node Type Colors'),
    '#description' => t('Colors for node types. If enabled, you may set colors for each node type below.'),
  );
  $form['fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('Node Type Colors'),
    '#collapsible' => TRUE,
    '#collapsed' => !variable_get('fullcalendar_color_node_types_enabled', FALSE),
  );
  $form['fieldset']['fullcalendar_color_node_types_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable colors for node types.'),
    '#default_value' => variable_get('fullcalendar_color_node_types_enabled', FALSE),
  );
  $types = node_type_get_types();
  $names = node_type_get_names();
  foreach ($names as $key => $name) {
    $type = $types[$key];
    $form['fieldset']['fullcalendar_color_node_type_' . $type->type] = array(
      '#title' => t($type->name),
      '#type' => 'textfield',
      '#attributes' => array('class' => array('colorpicker-input')),
      '#default_value' => variable_get('fullcalendar_color_node_type_' . $type->type, '#ffffff'),
      '#size' => 7,
      '#maxlength' => 7,
      '#states' => array(
        'visible' => array(
          ':input[name="fullcalendar_color_node_types_enabled"]' => array('checked' => TRUE),
        ),
      ),
    );
  }

  return system_settings_form($form);
}

/**
 * General configuration form for setting colors for Fullcalendar events.
 *
 * @see - http://drupal.org/node/1041068
 * @see - patch for fullcalendar.module at http://drupal.org/node/1041068#comment-4196492
 */
function fullcalendar_admin_color_taxonomy_settings() {

  $form = _load_colorpicker();
  
  /**
   * Taxonomies - show fieldset per taxonomy.
   */
  $form['taxonomy_colors'] = array(
    '#type' => 'item',
    '#title' => t('Taxonomy Colors'),
    '#description' => t('Colors on a per-taxonomy basis. After enabling a vocabulary, you can set colors for individual taxonomy terms below.'),
  );
  $taxonomies = taxonomy_get_vocabularies();
  $options = array();
  foreach ($taxonomies as $vid => $vocab) {
    $options[$vid] = $vocab->name;
    $form[$vid] = array(
      '#type' => 'fieldset',
      '#title' => $vocab->name,
      '#collapsible' => TRUE,
      '#collapsed' => !variable_get('fullcalendar_color_taxo_' . $vid . '_enabled', FALSE),
    );
    $form[$vid]['fullcalendar_color_taxo_' . $vid . '_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable this vocabulary.'),
      '#default_value' => variable_get('fullcalendar_color_taxo_' . $vid . '_enabled', FALSE),
    );

    if(variable_get('fullcalendar_color_taxo_' . $vid . '_enabled')) {
      // If enabled, get all the terms in the taxonomy.
      $term_ids = taxonomy_get_tree($vid);
      // Print textfield for each term, where user can add a hex value for a color.
      // @todo - Look into moving these into a custom db table, rather than variables...
      foreach ($term_ids as $tid => $term) {
        $form[$vid]['fullcalendar_color_taxo_term_' . $tid] = array(
          '#title' => t($term->name),
          '#type' => 'textfield',
          '#attributes' => array('class' => array('colorpicker-input')),
          '#default_value' => variable_get('fullcalendar_color_taxo_term_' . $tid, '#ffffff'),
          '#size' => 7,
          '#maxlength' => 7,
        );
      }
    } 
  }

  return system_settings_form($form);
}

/**
 * General configuration form for setting colors for Fullcalendar events.
 *
 * @see - http://drupal.org/node/1041068
 * @see - patch for fullcalendar.module at http://drupal.org/node/1041068#comment-4196492
 */
function fullcalendar_admin_color_og_settings() {

  $form = _load_colorpicker();

  /**
   * Groups (og)
   *
   * @todo - Allow user to enable/disable group color selection.
   * @todo - Allow user to set colors per group.
   */
  if(module_exists('og')) {
    $form['og_colors'] = array(
      '#type' => 'fieldset',
      '#title' => t('Group Colors'),
      '#description' => t('Colors for organic groups. If enabled, you may set colors for each group below.'),
    );
    $form['fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Group Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => !variable_get('fullcalendar_color_groups_enabled', FALSE),
    );
    $form['fieldset']['fullcalendar_color_groups_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable colors for groups.'),
      '#default_value' => variable_get('fullcalendar_color_groups_enabled', FALSE),
    );
    /**
     * @todo - Get a listing of all the groups, display it like node types above.
     */
  }

  return system_settings_form($form);
}

/**
 * Loads a farbtastic colorpicker on top of the page.
 */
function _load_colorpicker() {
  // CSS for our custom form.
  drupal_add_css(drupal_get_path('module', 'fullcalendar_colors') . '/fullcalendar_colors.admin.css');
  // JS for our Farbtastic integration.
  drupal_add_js(drupal_get_path('module', 'fullcalendar_colors') . '/fullcalendar_colors.admin.js');
  // CSS for Farbtastic color picker.
  drupal_add_css('misc/farbtastic/farbtastic.css');
  // JS for Farbtastic color picker.
  drupal_add_js('misc/farbtastic/farbtastic.js');

  /**
   * Colorpicker
   */
  $form['color_picker'] = array(
    '#type' => 'item',
    '#markup' => '<div id="colorpicker"></div>'
  );
  
  return $form;
}
