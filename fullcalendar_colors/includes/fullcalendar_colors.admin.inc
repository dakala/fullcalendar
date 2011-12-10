<?php

/**
 * @file
 * Color page callbacks for the FullCalendar colors module.
 */

/**
 * Configuration form for coloring node types.
 */
function fullcalendar_colors_admin_node_type_settings() {
  // CSS for our custom form.
  ctools_add_css('fullcalendar_colors.admin', 'fullcalendar_colors');
  $form = colors_load_colorpicker();

  $form['node_type_colors'] = array(
    '#type' => 'item',
    '#title' => t('Node type colors'),
    '#description' => t('Colors for node types. If enabled, you may set colors for each node type below.'),
  );
  $form['fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('Node type colors'),
    '#collapsible' => TRUE,
    '#collapsed' => !variable_get('fullcalendar_colors_node_types_enabled', FALSE),
  );
  $form['fieldset']['fullcalendar_colors_node_types_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable colors for node types'),
    '#default_value' => variable_get('fullcalendar_colors_node_types_enabled', FALSE),
  );
  foreach (node_type_get_names() as $type => $name) {
    $class = 'fullcalendar_colors_node_type_' . $type;
    $colors = colors_get_colors($class, 'fullcalendar_colors');

    $form['fieldset'][$class] = array(
      '#title' => t($name),
      '#type' => 'textfield',
      '#attributes' => array('class' => array('colorpicker-input')),
      '#default_value' => $colors['background'],
      '#size' => 7,
      '#maxlength' => 7,
      '#states' => array(
        'visible' => array(
          ':input[name="fullcalendar_colors_node_types_enabled"]' => array('checked' => TRUE),
        ),
      ),
    );
  }

  $form['actions']['#type'] = 'actions';
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );
  $form['#submit'][] = 'fullcalendar_colors_admin_node_type_settings_submit';

  return $form;
}

/**
 * Submit handler for fullcalendar_colors_admin_node_type_settings.
 */
function fullcalendar_colors_admin_node_type_settings_submit($form, &$form_state) {
  $values = $form_state['values'];

  variable_set('fullcalendar_colors_node_types_enabled', $values['fullcalendar_colors_node_types_enabled']);

  // Save the node colors.
  foreach (node_type_get_names() as $type => $name) {
    $class = 'fullcalendar_colors_node_type_' . $type;
    fullcalendar_colors_set_color($class, $values[$class]);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Configuration form for coloring taxonomy.
 */
function fullcalendar_colors_admin_taxonomy_settings() {
  // CSS for our custom form.
  ctools_add_css('fullcalendar_colors.admin', 'fullcalendar_colors');
  $form = colors_load_colorpicker();

  $form['taxonomy_colors'] = array(
    '#type' => 'item',
    '#title' => t('Taxonomy colors'),
    '#description' => t('Colors on a per-taxonomy basis. After enabling a vocabulary, you can set colors for individual taxonomy terms below.'),
  );
  foreach (taxonomy_get_vocabularies() as $vid => $vocab) {
    $form[$vid] = array(
      '#type' => 'fieldset',
      '#title' => $vocab->name,
      '#collapsible' => TRUE,
      '#collapsed' => !variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled', FALSE),
    );
    $form[$vid]['fullcalendar_colors_taxo_' . $vid . '_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable this vocabulary'),
      '#default_value' => variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled', FALSE),
    );

    if (variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled')) {
      // If enabled, get all the terms in the taxonomy.
      $term_ids = taxonomy_get_tree($vid);
      // Print textfield for each term so user can add a hex value for a color.
      foreach ($term_ids as $term) {
        $class = 'fullcalendar_colors_taxo_term_' . $term->tid;
        $colors = colors_get_colors($class, 'fullcalendar_colors');
        $form[$vid][$class] = array(
          '#title' => t($term->name),
          '#type' => 'textfield',
          '#attributes' => array('class' => array('colorpicker-input')),
          '#default_value' => $colors['background'],
          '#size' => 7,
          '#maxlength' => 7,
        );
      }
    }
  }

  $form['actions']['#type'] = 'actions';
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );
  $form['#submit'][] = 'fullcalendar_colors_admin_taxonomy_settings_submit';

  return $form;
}

/**
 * Submit handler for fullcalendar_colors_admin_taxonomy_settings.
 */
function fullcalendar_colors_admin_taxonomy_settings_submit($form, &$form_state) {
  $values = $form_state['values'];

  foreach (taxonomy_get_vocabularies() as $vid => $vocab) {
    if (variable_get('fullcalendar_colors_taxo_' . $vid . '_enabled')) {
      $term_ids = taxonomy_get_tree($vid);
      foreach ($term_ids as $term) {
        $class = 'fullcalendar_colors_taxo_term_' . $term->tid;
        fullcalendar_colors_set_color($class, $values[$class]);
      }
    }
    variable_set('fullcalendar_colors_taxo_' . $vid . '_enabled', $values['fullcalendar_colors_taxo_' . $vid . '_enabled']);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Configuration form for coloring organic groups (og).
 */
function fullcalendar_colors_admin_og_settings() {
  // CSS for our custom form.
  ctools_add_css('fullcalendar_colors.admin', 'fullcalendar_colors');
  $form = colors_load_colorpicker();

  if (module_exists('og')) {
    $form['og_colors'] = array(
      '#type' => 'item',
      '#title' => t('Group colors'),
      '#description' => t('Colors for organic groups. If enabled, you may set colors for each group below.'),
    );
    $form['fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Group colors'),
      '#collapsible' => TRUE,
      '#collapsed' => !variable_get('fullcalendar_colors_groups_enabled', FALSE),
    );
    $form['fieldset']['fullcalendar_colors_groups_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable colors for groups'),
      '#default_value' => variable_get('fullcalendar_colors_groups_enabled', FALSE),
    );
    foreach (og_get_all_group() as $gid) {
      $class = 'fullcalendar_color_group_' . $gid;
      $colors = colors_get_colors($class, 'fullcalendar_colors');

      $form['fieldset'][$class] = array(
        '#title' => og_label($gid),
        '#type' => 'textfield',
        '#attributes' => array('class' => array('colorpicker-input')),
        '#default_value' => $colors['background'],
        '#size' => 7,
        '#maxlength' => 7,
        '#states' => array(
          'visible' => array(
            ':input[name="fullcalendar_color_groups_enabled"]' => array('checked' => TRUE),
          ),
        ),
      );
    }
  }

  $form['actions']['#type'] = 'actions';
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );
  $form['#submit'][] = 'fullcalendar_colors_admin_og_settings_submit';

  return $form;
}

/**
 * Submit handler for fullcalendar_colors_admin_og_settings.
 */
function fullcalendar_colors_admin_og_settings_submit($form, &$form_state) {
  $values = $form_state['values'];
  variable_set('fullcalendar_colors_groups_enabled', $values['fullcalendar_colors_groups_enabled']);

  // Save the node colors.
  foreach (og_get_all_group() as $gid) {
    $class = 'fullcalendar_color_group_' . $gid;
    fullcalendar_colors_set_color($class, $values[$class]);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Configuration form for coloring user roles.
 */
function fullcalendar_colors_admin_user_role_settings() {
  // CSS for our custom form.
  ctools_add_css('fullcalendar_colors.admin', 'fullcalendar_colors');
  $form = colors_load_colorpicker();

  $form['user_role_colors'] = array(
    '#type' => 'item',
    '#title' => t('User role colors'),
    '#description' => t('Colors for user roles. If enabled, you may set colors for each user role below.'),
  );
  $form['fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('User roles colors'),
    '#collapsible' => TRUE,
    '#collapsed' => !variable_get('fullcalendar_colors_user_roles_enabled', FALSE),
  );
  $form['fieldset']['fullcalendar_colors_user_roles_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable colors for user roles'),
    '#default_value' => variable_get('fullcalendar_colors_user_roles_enabled', FALSE),
  );
  foreach (user_roles() as $role_id => $role) {
    $class = 'fullcalendar_colors_user_role_' . $role_id;
    $colors = colors_get_colors($class, 'fullcalendar_colors');

    $form['fieldset'][$class] = array(
      '#title' => t($role),
      '#type' => 'textfield',
      '#attributes' => array('class' => array('colorpicker-input')),
      '#default_value' => $colors['background'],
      '#size' => 7,
      '#maxlength' => 7,
      '#states' => array(
        'visible' => array(
          ':input[name="fullcalendar_colors_user_roles_enabled"]' => array('checked' => TRUE),
        ),
      ),
    );
  }

  $form['actions']['#type'] = 'actions';
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );
  $form['#submit'][] = 'fullcalendar_colors_admin_user_role_settings_submit';

  return $form;
}

/**
 * Submit handler for fullcalendar_colors_admin_node_type_settings.
 */
function fullcalendar_colors_admin_user_role_settings_submit($form, &$form_state) {
  $values = $form_state['values'];
  variable_set('fullcalendar_colors_user_roles_enabled', $values['fullcalendar_colors_user_roles_enabled']);

  // Save the user roles colors.
  foreach (user_roles() as $role_id => $role) {
    $class = 'fullcalendar_colors_user_role_' . $role_id;
    fullcalendar_colors_set_color($class, $values[$class]);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Configuration form for color settings.
 */
function fullcalendar_colors_admin_settings() {
  $form = colors_load_colorpicker();
  $default_colors = colors_get_colors('fullcalendar_colors_default');

  $form['default_color'] = array(
    '#type' => 'item',
    '#title' => t('Default color'),
    '#tree' => TRUE,
    'input' => array(
      '#title' => t('default color'),
      '#type' => 'textfield',
      '#attributes' => array('class' => array('colorpicker-input')),
      '#default_value' => $default_colors['background'],
      '#size' => 7,
      '#maxlength' => 7,
      '#title_display' => 'invisible',
    ),
  );

  $form['process_order'] = array(
    '#tree' => TRUE,
    'info' => array(
      '#type' => 'item',
      '#title' => t('Process order'),
    ),
    'enabled' => array(
      '#type' => 'checkbox',
      '#title' => t('Change the CSS processing order.'),
      '#default_value' => variable_get('fullcalendar_colors_process_order_enabled', FALSE),
      '#description' => t('Color order is cascading, CSS from modules at the bottom will override the top.'),
    ),
  );

  fullcalendar_colors_include_api();
  $modules = array();
  foreach (module_implements('fullcalendar_classes') as $weight => $module) {
    $modules[$module] = variable_get('fullcalendar_colors_weight_' . $module, $weight);
  }

  // FullCalendar's colors will always be processed first, remove it and sort
  // the list of modules.
  unset($modules['fullcalendar']);
  asort($modules);

  $form['modules'] = array(
    '#tree' => TRUE,
  );
  $rows = array();
  foreach ($modules as $module => $weight) {
    // Get the module name.
    $path = drupal_get_path('module', $module) . '/' . $module . '.info';
    $info = drupal_parse_info_file($path);

    $form['modules'][$module]['#name'] = $info['name'];
    $form['modules'][$module]['#weight'] = $weight;
    $form['modules'][$module]['weight'] = array(
      '#type' => 'textfield',
      '#title' => t('Weight for @title', array('@title' => $module)),
      '#title_display' => 'invisible',
      '#size' => 4,
      '#default_value' => $weight,
      '#attributes' => array('class' => array('fullcalendar-color-weight')),
    );
  }
  $form['order_settings'] = array(
    '#type' => 'container',
    '#states' => array(
      'visible' => array(
        'input[name="process_order[enabled]"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['actions'] = array(
    '#type' => 'actions',
    'submit' => array(
      '#type' => 'submit',
      '#value' => t('Save settings'),
      '#submit' => array('fullcalendar_colors_admin_settings_submit'),
    ),
  );

  return $form;
}

/**
 * Form submit handler for the fullcalendar_colors_admin_settings() form.
 */
function fullcalendar_colors_admin_settings_submit($form, &$form_state) {
  fullcalendar_colors_set_color('fullcalendar_colors_default', $form_state['values']['default_color']['input']);
  variable_set('fullcalendar_colors_process_order_enabled', $form_state['values']['process_order']['enabled']);

  foreach ($form_state['values']['modules'] as $module => $weight) {
    if ($form['modules'][$module]['#weight'] != $weight['weight']) {
      variable_set('fullcalendar_colors_weight_' . $module, $weight['weight']);
    }
  }
}

/**
 * Returns HTML for the settings form.
 *
 * @param array $variables
 *   An associative array containing:
 *   - form: A render element representing the form.
 */
function theme_fullcalendar_colors_admin_settings($variables) {
  $form = $variables['form'];

  $rows = array();
  foreach (element_children($form['modules']) as $module) {
    $row = array();
    $row[] = $form['modules'][$module]['#name'];
    $row[] = drupal_render($form['modules'][$module]['weight']);

    $rows[] = array('data' => $row, 'class' => array('draggable'));
  }

  $form['order_settings']['table'] = array(
    '#theme' => 'table',
    '#header' => array(
      t('Module'),
      t('Weight'),
    ),
    '#rows' => $rows,
    '#attributes' => array('id' => 'fullcalender-colors-settings'),
  );
  drupal_add_tabledrag('fullcalender-colors-settings', 'order', 'sibling', 'fullcalendar-color-weight');

  return drupal_render_children($form);
}
