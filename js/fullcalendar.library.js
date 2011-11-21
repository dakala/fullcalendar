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

Drupal.fullcalendar.update = function(result) {
  var fcStatus = $(result.dom_id).find('.fullcalendar-status');
  if (fcStatus.text() === '') {
    fcStatus.html(result.msg).slideDown();
  }
  else {
    fcStatus.effect('highlight', {}, 5000);
  }
  Drupal.attachBehaviors();
  return false;
};

/**
 * Parse Drupal events from the DOM.
 */
Drupal.fullcalendar.parseEvents = function(dom_id, calendar, callback) {
  var events = [];
  // Drupal events.
  $('.fullcalendar-event-details', calendar).each(function() {
    var event = $(this);
    events.push({
      field: event.attr('field'),
      index: event.attr('index'),
      eid: event.attr('eid'),
      entity_type: event.attr('entity_type'),
      title: event.attr('title'),
      start: event.attr('start'),
      end: event.attr('end'),
      url: event.attr('href'),
      allDay: (event.attr('allDay') === '1'),
      className: event.attr('cn'),
      editable: (event.attr('editable') === '1'),
      dom_id: dom_id
    });
  });
  callback(events);
};

})(jQuery);
