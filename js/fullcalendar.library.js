(function ($) {

Drupal.fullCalendar = Drupal.fullCalendar || {};
Drupal.fullCalendar.navigate = false;
Drupal.fullCalendar.options = {
  global: {}
};
Drupal.fullCalendar.droppableCallbacks = {};

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
Drupal.fullCalendar.registerOptions = function(name, options, dom_id) {
  dom_id = dom_id || 'global';
  Drupal.fullCalendar.options[dom_id] = Drupal.fullCalendar.options[dom_id] || {};
  Drupal.fullCalendar.options[dom_id][name] = options;
};

/**
 * Retrieve FullCalendar options for a specific view.
 *
 * Gathers all global and view-specific settings.
 *
 * @param dom_id
 *   The dom id of the FullCalendar view.
 */
Drupal.fullCalendar.getOptions = function(dom_id) {
  Drupal.fullCalendar.options[dom_id] = Drupal.fullCalendar.options[dom_id] || {};
  return $.extend({}, Drupal.fullCalendar.options.global, Drupal.fullCalendar.options[dom_id]);
};

})(jQuery);
