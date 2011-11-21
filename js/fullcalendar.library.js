(function ($) {

Drupal.fullcalendar = {};
Drupal.fullcalendar.navigate = false;
Drupal.fullcalendar.options = {
  global: {}
};
Drupal.fullcalendar.droppableCallbacks = {};

// Alias old fullCalendar namespace.
Drupal.fullCalendar = Drupal.fullcalendar;

/**
 * Add FullCalendar options for a specific view.
 *
 * If the dom id is not provided, the options will be added for all views.
 *
 * @param name
 *   The module name.
 * @param options
 *   An object containing official FullCalendar options.
 * @param dom_id
 *   (optional) The dom id of the FullCalendar view.
 *
 * @see http://arshaw.com/fullcalendar/docs
 */
Drupal.fullcalendar.registerOptions = function(name, options, dom_id) {
  dom_id = dom_id || 'global';
  Drupal.fullcalendar.options[dom_id] = Drupal.fullcalendar.options[dom_id] || {};
  Drupal.fullcalendar.options[dom_id][name] = options;
};

/**
 * Retrieve FullCalendar options for a specific view.
 *
 * Gathers all global and view-specific settings.
 *
 * @param dom_id
 *   The dom id of the FullCalendar view.
 */
Drupal.fullcalendar.getOptions = function(dom_id) {
  Drupal.fullcalendar.options[dom_id] = Drupal.fullcalendar.options[dom_id] || {};
  return $.extend({}, Drupal.fullcalendar.options.global, Drupal.fullcalendar.options[dom_id]);
};

// Alias old fullCalendar namespace.
Drupal.fullCalendar = Drupal.fullcalendar;

})(jQuery);
