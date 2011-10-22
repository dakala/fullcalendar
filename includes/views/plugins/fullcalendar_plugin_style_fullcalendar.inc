<?php

/**
 * @file
 * Contains the FullCalendar style plugin.
 */

class fullcalendar_plugin_style_fullcalendar extends views_plugin_style {
  var $formats = array(
    '12' => array(
      'time' => 'h:mm{ - h:mm}',
      'axis' => 'h(:mm)t',
    ),
    '24' => array(
      'time' => 'HH:mm{ - HH:mm}',
      'axis' => 'HH(:mm)',
    ),
    'mdy' => array(
      'title' => array(
        'month' => 'MMMM yyyy',
        'week' => 'MMM d[ yyyy]{ - [ MMM] d yyyy}',
        'day' => 'dddd, MMM d, yyyy',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd M/d',
        'day' => 'dddd M/d',
      ),
    ),
    'dmy' => array(
      'title' => array(
        'month' => 'MMMM yyyy',
        'week' => 'd [ MMM][ yyyy]{ - d MMM yyyy}',
        'day' => 'dddd, d MMM, yyyy',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd d/M',
        'day' => 'dddd d/M',
      ),
    ),
    'ymd' => array(
      'title' => array(
        'month' => 'yyyy MMMM',
        'week' => 'yyyy MMM d{ - [ yyyy][ MMM] d}',
        'day' => 'yyyy MMM d, dddd ',
      ),
      'column' => array(
        'month' => 'ddd',
        'week' => 'ddd M/d',
        'day' => 'dddd M/d',
      ),
    ),
  );

  var $time = '12';
  var $date = 'mdy';

  function init(&$view, &$display, $options = NULL) {
    parent::init($view, $display, $options);

    if (empty($options)) {
      return;
    }

    // Set up a rename map from the old names to new names.
    $rename_map = array(
      'times' => array(
        'fc_default_date' => 'default_date',
        'fc_date' => 'date',
        'fc_timeformat' => 'timeformat',
      ),
      'header' => array(
        'fc_left' => 'left',
        'fc_center' => 'center',
        'fc_right' => 'right',
      ),
      'modules' => array(
        'fc_theme' => 'theme',
        'fc_window' => 'sameWindow',
        'fc_url_colorbox' => 'colorbox',
        'fc_url_colorbox_class' => 'colorboxClass',
        'fc_url_colorbox_height' => 'colorboxHeight',
        'fc_url_colorbox_width' => 'colorboxWidth',
      ),
      'display' => array(
        'fc_view' => 'defaultView',
        'fc_firstday' => 'firstDay',
        'fc_weekmode' => 'weekMode',
        'fc_content_height' => 'contentHeight',
      ),
    );

    $new_options = array();
    foreach ($options as $grouping => $option) {
      if (!isset($rename_map[$grouping])) {
        continue;
      }
      foreach ($rename_map[$grouping] as $old_option => $new_option) {
        if (!isset($options[$grouping][$old_option])) {
          continue;
        }

        // All new settings aren't nested, except date and default_date.
        if ($grouping == 'times' && in_array($old_option, array('fc_default_date', 'fc_date'))) {
          $new_options[$grouping][$new_option] = $options[$grouping][$old_option];
        }
        else {
          $new_options[$new_option] = $options[$grouping][$old_option];
        }
      }
    }

    // Set out new variable for later usage.
    $this->time = empty($options['times']['fc_clock']) ? '12' : '24';

    // Add new options to defaults.
    $this->unpack_options($this->options, array_filter($new_options));

    // Unset the obsolete settings.
    unset($this->options['display']);
    unset($this->options['modules']);
    unset($this->options['header']);
    unset($this->options['times']['fc_clock']);
    unset($this->options['times']['fc_date']);
    unset($this->options['times']['fc_default_date']);
    unset($this->options['times']['fc_timeformat']);
  }

  function option_definition() {
    $time_format = $this->formats[$this->time];
    $date_format = $this->formats[$this->date];

    $options = array(
      'defaultView' => array('default' => 'month'),
      'firstDay' => array('default' => '0'),
      'weekMode' => array('default' => 'fixed'),
      'left' => array('default' => 'today prev,next'),
      'center' => array('default' => 'title'),
      'right' => array('default' => 'month agendaWeek agendaDay'),
      'timeformat' => array('default' => $time_format['time']),
      'advanced' => array('default' => FALSE),
      'axisformat' => array('default' => $time_format['axis']),
      'timeformatMonth' => array('default' => $time_format['time']),
      'titleformatMonth' => array('default' => $date_format['title']['month']),
      'columnformatMonth' => array('default' => $date_format['column']['month']),
      'timeformatWeek' => array('default' => $time_format['time']),
      'titleformatWeek' => array('default' => $date_format['title']['week']),
      'columnformatWeek' => array('default' => $date_format['column']['week']),
      'timeformatDay' => array('default' => $time_format['time']),
      'titleformatDay' => array('default' => $date_format['title']['day']),
      'columnformatDay' => array('default' => $date_format['column']['day']),
      'theme' => array('default' => TRUE),
      'sameWindow' => array('default' => FALSE),
      'colorbox' => array('default' => FALSE),
      'colorboxClass' => array('default' => '#content'),
      'colorboxWidth' => array('default' => '80%'),
      'colorboxHeight' => array('default' => '80%'),
      'contentHeight' => array('default' => ''),
      'droppable' => array('default' => ''),
    );

    // Nest these explicitly so that they can be more easily found later.
    $options['times'] = array(
      'contains' => array(
        'default_date' => array('default' => FALSE),
        'date' => array('default' => array(
          'year' => '',
          'month' => '',
          'day' => '',
        )),
      ),
    );
    $options['fields'] = array(
      'contains' => array(
        'title_field' => array('default' => ''),
        'url_field' => array('default' => ''),
        'date_field' => array('default' => ''),
        'title' => array('default' => FALSE),
        'url' => array('default' => FALSE),
        'date' => array('default' => FALSE),
      ),
    );

    return $options;
  }

  function options_form(&$form, &$form_state) {
    $form['#pre_render'][] = 'views_ui_pre_render_add_fieldset_markup';

    $form['display'] = array(
      '#type' => 'fieldset',
      '#title' => t('Display settings'),
      '#collapsible' => TRUE,
    );
    $form['header'] = array(
      '#type' => 'fieldset',
      '#title' => t('Header settings'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/display/header', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['times'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time/date settings'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/utilities/formatDate', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['style'] = array(
      '#type' => 'fieldset',
      '#title' => t('Style settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );

    $form['defaultView'] = array(
      '#type' => 'select',
      '#title' => t('Initial display'),
      '#options' => array(
        'month' => 'Month',
        'agendaWeek' => 'Week (Agenda)',
        'basicWeek' => 'Week (Basic)',
        'agendaDay' => 'Day (Agenda)',
        'basicDay' => 'Day (Basic)',
      ),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/views/Available_Views', array('attributes' => array('target' => '_blank'))),
      '#default_value' => $this->options['defaultView'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['firstDay'] = array(
      '#type' => 'select',
      '#title' => t('Week starts on'),
      '#options' => array(
        '0' => t('Sunday'),
        '1' => t('Monday'),
      ),
      '#default_value' => $this->options['firstDay'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['weekMode'] = array(
      '#type' => 'select',
      '#title' => t('Week mode'),
      '#options' => array(
        'fixed' => 'Fixed',
        'liquid' => 'Liquid',
        'variable' => 'Variable',
      ),
      '#default_value' => $this->options['weekMode'],
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/display/weekMode', array('attributes' => array('target' => '_blank'))),
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#fieldset' => 'display',
    );
    $form['left'] = array(
      '#type' => 'textfield',
      '#title' => t('Left'),
      '#default_value' => $this->options['left'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['center'] = array(
      '#type' => 'textfield',
      '#title' => t('Center'),
      '#default_value' => $this->options['center'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['right'] = array(
      '#type' => 'textfield',
      '#title' => t('Right'),
      '#default_value' => $this->options['right'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'header',
    );
    $form['times']['default_date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom initial date'),
      '#description' => t('If unchecked, the calendar will load the current date.'),
      '#default_value' => $this->options['times']['default_date'],
      '#fieldset' => 'times',
    );
    $form['times']['date'] = array(
      '#type' => 'date',
      '#title' => t('Custom initial date'),
      '#title_display' => 'invisible',
      '#default_value' => $this->options['times']['date'],
      '#dependency' => array('edit-style-options-times-default-date' => array(1)),
      '#fieldset' => 'times',
    );
    $form['timeformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Time format'),
      '#default_value' => $this->options['timeformat'],
      '#size' => '30',
      '#fieldset' => 'times',
      '#dependency' => array('edit-style-options-advanced' => array(0)),
    );
    $form['advanced'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable advanced time and date format settings'),
      '#default_value' => $this->options['advanced'],
      '#fieldset' => 'times',
    );
    $form['axisformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Axis format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/agenda/axisFormat', array('attributes' => array('target' => '_blank'))),
      '#default_value' => $this->options['axisformat'],
      '#size' => '30',
      '#fieldset' => 'times',
      '#dependency' => array('edit-style-options-advanced' => array(1)),
    );
    $form['timeformatMonth'] = array(
      '#type' => 'textfield',
      '#title' => t('Month'),
      '#default_value' => $this->options['timeformatMonth'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'time',
    );
    $form['titleformatMonth'] = array(
      '#type' => 'textfield',
      '#title' => t('Month'),
      '#default_value' => $this->options['titleformatMonth'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'title',
    );
    $form['columnformatMonth'] = array(
      '#type' => 'textfield',
      '#title' => t('Month'),
      '#default_value' => $this->options['columnformatMonth'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'column',
    );
    $form['timeformatWeek'] = array(
      '#type' => 'textfield',
      '#title' => t('Week'),
      '#default_value' => $this->options['timeformatWeek'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'time',
    );
    $form['titleformatWeek'] = array(
      '#type' => 'textfield',
      '#title' => t('Week'),
      '#default_value' => $this->options['titleformatWeek'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'title',
    );
    $form['columnformatWeek'] = array(
      '#type' => 'textfield',
      '#title' => t('Week'),
      '#default_value' => $this->options['columnformatWeek'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'column',
    );
    $form['timeformatDay'] = array(
      '#type' => 'textfield',
      '#title' => t('Day'),
      '#default_value' => $this->options['timeformatDay'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'time',
    );
    $form['titleformatDay'] = array(
      '#type' => 'textfield',
      '#title' => t('Day'),
      '#default_value' => $this->options['titleformatDay'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'title',
    );
    $form['columnformatDay'] = array(
      '#type' => 'textfield',
      '#title' => t('Day'),
      '#default_value' => $this->options['columnformatDay'],
      '#prefix' => '<div class="views-left-30">',
      '#suffix' => '</div>',
      '#size' => '30',
      '#fieldset' => 'column',
    );
    $form['time'] = array(
      '#type' => 'fieldset',
      '#title' => t('Time format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/timeFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#dependency' => array('edit-style-options-advanced' => array(1)),
    );
    $form['title'] = array(
      '#type' => 'fieldset',
      '#title' => t('Title format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/titleFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#dependency' => array('edit-style-options-advanced' => array(1)),
    );
    $form['column'] = array(
      '#type' => 'fieldset',
      '#title' => t('Column format'),
      '#description' => l(t('More info'), 'http://arshaw.com/fullcalendar/docs/text/columnFormat', array('attributes' => array('target' => '_blank'))),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#fieldset' => 'times',
      '#dependency' => array('edit-style-options-advanced' => array(1)),
    );
    $form['theme'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use jQuery UI Theme'),
      '#default_value' => $this->options['theme'],
      '#fieldset' => 'style',
    );
    if (module_exists('colorbox')) {
      $form['colorbox'] = array(
        '#type' => 'checkbox',
        '#title' => t('Open events with Colorbox'),
        '#default_value' => $this->options['colorbox'],
        '#fieldset' => 'style',
      );
      $form['colorboxIFrame'] = array(
        '#type' => 'checkbox',
        '#title' => t('Open events in iFrame'),
        '#default_value' => $this->options['colorboxIFrame'],
        '#fieldset' => 'colorboxSettings',
      );
      $form['colorboxClass'] = array(
        '#type' => 'textfield',
        '#title' => t('Classname or ID selector'),
        '#default_value' => $this->options['colorboxClass'],
        '#fieldset' => 'colorboxSettings',
      );
      $form['colorboxWidth'] = array(
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#default_value' => $this->options['colorboxWidth'],
        '#fieldset' => 'colorboxSettings',
      );
      $form['colorboxHeight'] = array(
        '#type' => 'textfield',
        '#title' => t('Height'),
        '#default_value' => $this->options['colorboxHeight'],
        '#fieldset' => 'colorboxSettings',
      );
      $form['colorboxSettings'] = array(
        '#type' => 'fieldset',
        '#title' => t('Colorbox settings'),
        '#fieldset' => 'style',
        '#dependency' => array('edit-style-options-colorbox' => array(1)),
      );
    }
    $form['sameWindow'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open events in same window'),
      '#default_value' => $this->options['sameWindow'],
      '#fieldset' => 'style',
    );
    $form['contentHeight'] = array(
      '#type' => 'textfield',
      '#title' => t('Calendar height'),
      '#size' => 4,
      '#default_value' => $this->options['contentHeight'],
      '#field_suffix' => 'px',
      '#fieldset' => 'style',
    );
    if (module_implements('fullcalendar_droppable')) {
      $form['droppable'] = array(
        '#type' => 'checkbox',
        '#title' => t('Allow external events to be added via drag and drop'),
        '#default_value' => $this->options['droppable'],
        '#fieldset' => 'style',
      );
    }

    if (module_exists('colorbox')) {
      $form['sameWindow']['#dependency'] = array('edit-style-options-colorbox' => array(0));
    }

    // Get the regular fields.
    $field_options = $this->display->handler->get_field_labels();
    // Get the date fields.
    $date_fields = $this->fullcalendar_parse_fields();

    $form['fields'] = array(
      '#type' => 'fieldset',
      '#title' => t('Customize fields'),
      '#description' => t('Add fields to the view in order to customize fields below.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['fields']['title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom title'),
      '#default_value' => $this->options['fields']['title'],
      '#fieldset' => 'fields',
    );
    $form['fields']['title_field'] = array(
      '#type' => 'select',
      '#title' => t('Title Field'),
      '#options' => $field_options,
      '#default_value' => $this->options['fields']['title_field'],
      '#description' => t('Choose the field with the custom title.'),
      '#process' => array('form_process_select'),
      '#dependency' => array('edit-style-options-fields-title' => array(1)),
      '#fieldset' => 'fields',
    );
    $form['fields']['url'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom redirect URL'),
      '#default_value' => $this->options['fields']['url'],
      '#fieldset' => 'fields',
    );
    $form['fields']['url_field'] = array(
      '#type' => 'select',
      '#title' => t('URL Field'),
      '#options' => $field_options,
      '#default_value' => $this->options['fields']['url_field'],
      '#description' => t('Choose the field with the custom link.'),
      '#process' => array('form_process_select'),
      '#dependency' => array('edit-style-options-fields-url' => array(1)),
      '#fieldset' => 'fields',
    );
    $form['fields']['date'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use a custom date field'),
      '#default_value' => $this->options['fields']['date'],
      '#fieldset' => 'fields',
    );
    $form['fields']['date_field'] = array(
      '#type' => 'select',
      '#title' => t('Date Fields'),
      '#options' => $date_fields,
      '#default_value' => $this->options['fields']['date_field'],
      '#description' => t('Select one or more date fields.'),
      '#multiple' => TRUE,
      '#size' => count($date_fields),
      '#process' => array('form_process_select'),
      '#dependency' => array('edit-style-options-fields-date' => array(1)),
      '#fieldset' => 'fields',
    );

    // Disable form elements when not needed.
    if (empty($field_options)) {
      $form['fields']['#description'] = t('All the options are hidden, you need to add fields first.');
      $form['fields']['title']['#type'] = 'hidden';
      $form['fields']['url']['#type'] = 'hidden';
      $form['fields']['date']['#type'] = 'hidden';
      $form['fields']['title_field']['#disabled'] = TRUE;
      $form['fields']['url_field']['#disabled'] = TRUE;
      $form['fields']['date_field']['#disabled'] = TRUE;
    }
    elseif (empty($date_fields)) {
      $form['fields']['date']['#type'] = 'hidden';
      $form['fields']['date_field']['#disabled'] = TRUE;
    }
  }

  function validate() {
    if ($this->display->display_plugin != 'default') {
      // Get the date fields.
      $date_fields = $this->fullcalendar_parse_fields();
      if (empty($date_fields)) {
        drupal_set_message(t('Display "@display" requires at least one date field.', array('@display' => $this->display->display_title)), 'error');
      }
    }

    return parent::validate();
  }

  function fullcalendar_parse_fields() {
    $labels = $this->display->handler->get_field_labels();
    $field_options = array();
    foreach ($this->display->handler->get_handlers('field') as $id => $field) {
      if (fullcalendar_field_is_date($field, TRUE)) {
        $field_options[$id] = $labels[$id];
      }
    }
    return $field_options;
  }
}
