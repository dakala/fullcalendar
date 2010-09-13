<?php
// $Id$
/**
 * @file
 * Contains the node view row style plugin.
 */

/**
 * Plugin which performs a node_view on the resulting object.
 *
 * Most of the code on this object is in the theme function.
 */
class views_plugin_node_fullcalendar extends views_plugin_row {
  // Basic properties that let the row style follow relationships.
  var $base_table = 'node';
  var $base_field = 'nid';

  function option_definition() {
    $options = parent::option_definition();
    $options['fullcalendar_url_field'] = array('default' => '');
    $options['fullcalendar_date_fields'] = array('default' => '');
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['fullcalendar_date_fields'] = array(
      '#type' => 'textarea',
      '#title' => t('Date Fields'),
      '#default_value' => $this->options['fullcalendar_date_fields'],
      '#description' => t('Normally this plugin uses the first node field that is either a date or a datetime field type.  However, if you wish to override this behavior, please enter the names of the fields to use instead.  For example "field_scheduled_for".  Enter one per line.  They will be searched in the order listed.  Enter "node_created" or "node_changed" to use the respective node timestamps.'),
    );
    $form['fullcalendar_url_field'] = array(
      '#type' => 'textfield',
      '#title' => t('URL Field'),
      '#default_value' => $this->options['fullcalendar_url_field'],
      '#description' => t('If the calendar items should not link directly to the node, enter the name of the cck field to use for the url instead.  For example "field_url"'),
    );
  }
  
  function render($row) {
    return theme($this->theme_functions(), $this->view, $this->options, $row, $this->field_alias);
  }
}
