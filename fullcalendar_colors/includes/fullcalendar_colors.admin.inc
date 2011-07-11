<?php

/**
 * @file
 * Color page callbacks for the FullCalendar colors module.
 */

/**
 * Configuration form for coloring node types.
 */
function fullcalendar_colors_admin_node_type_settings() {
  $form = _fullcalendar_colors_load_colorpicker();

  $form['node_type_colors'] = array(
    '#type' => 'item',
    '#title' => t('Node Type Colors'),
    '#description' => t('Colors for node types. If enabled, you may set colors for each node type below.'),
  );
  $form['fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('Node Type Colors'),
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

  // Save the node colors
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
  $form = _fullcalendar_colors_load_colorpicker();

  $form['taxonomy_colors'] = array(
    '#type' => 'item',
    '#title' => t('Taxonomy Colors'),
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
      // Print textfield for each term, where user can add a hex value for a color.
      // @todo - Look into moving these into a custom db table, rather than variables...
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
  $form = _fullcalendar_colors_load_colorpicker();

  if (module_exists('og')) {
    $form['og_colors'] = array(
      '#type' => 'fieldset',
      '#title' => t('Group Colors'),
      '#description' => t('Colors for organic groups. If enabled, you may set colors for each group below.'),
    );
    $form['fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Group Colors'),
      '#collapsible' => TRUE,
      '#collapsed' => !variable_get('fullcalendar_colors_groups_enabled', FALSE),
    );
    $form['fieldset']['fullcalendar_colors_groups_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable colors for groups'),
      '#default_value' => variable_get('fullcalendar_colors_groups_enabled', FALSE),
    );
    /**
     * @todo - Get a listing of all the groups, display it like node types above.
     */
  }

  return system_settings_form($form);
}

/**
 * Configuration form for coloring user roles.
 */
function fullcalendar_colors_admin_user_role_settings() {
  $form = _fullcalendar_colors_load_colorpicker();

  $form['user_role_colors'] = array(
    '#type' => 'item',
    '#title' => t('User Role Colors'),
    '#description' => t('Colors for user roles. If enabled, you may set colors for each user role below.'),
  );
  $form['fieldset'] = array(
    '#type' => 'fieldset',
    '#title' => t('User Roles Colors'),
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

  // Save the user roles colors
  foreach (user_roles() as $role_id => $role) {
    $class = 'fullcalendar_colors_user_role_' . $role_id;
    fullcalendar_colors_set_color($class, $values[$class]);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Loads a farbtastic colorpicker on top of the page.
 */
function _fullcalendar_colors_load_colorpicker() {
  // CSS for our custom form.
  ctools_add_css('fullcalendar_colors.admin', 'fullcalendar_colors');
  // JS for our Farbtastic integration.
  ctools_add_js('fullcalendar_colors.admin', 'fullcalendar_colors');
  // CSS and JS for Farbtastic color picker.
  drupal_add_library('system', 'farbtastic');

  // Colorpicker
  return array(
    'color_picker' => array(
      '#prefix' => '<div id="colorpicker">',
      '#suffix' => '</div>',
    ),
  );
}
