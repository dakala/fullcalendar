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
    $options['url_field'] = array('default' => '');
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['url_field'] = array(
      '#type' => 'textfield',
      '#title' => t('URL Field'),
      '#default_value' => $this->options['url_field'],
      '#description' => t('If the calendar items should not link directly to the node, enter the name of the cck field to use for the url instead.'),
    );
  }
  
  function render($row) {
    return theme($this->theme_functions(), $this->view, $this->options, $row, $this->field_alias);
  }
}
