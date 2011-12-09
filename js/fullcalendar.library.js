/**
 * @file
 * Provides FullCalendar defaults and functions.
 */

(function ($) {

Drupal.fullcalendar = {};
Drupal.fullcalendar.navigate = false;
Drupal.fullcalendar.options = {
  global: {}
};
Drupal.fullcalendar.droppableCallbacks = {};

// Alias old fullCalendar namespace.
Drupal.fullCalendar = Drupal.fullcalendar;

Drupal.fullcalendar.fullcalendar = function (dom_id) {
  this.$calendar = $(dom_id);
  this.$options = {};

  // Hide the failover display.
  $('.fullcalendar-content', this.$calendar).hide();

  // Allow other modules to overwrite options.
  var $extendedOptions = this.getOptions(dom_id);

  // Load the base FullCalendar options first.
  // @todo Use the weights system to order this.
  $.extend(this.$options, $extendedOptions.fullcalendar);
  delete $extendedOptions.fullcalendar;

  // Loop through additional options, overwriting the defaults.
  for (var extendedOption in $extendedOptions) {
    if ($extendedOptions.hasOwnProperty(extendedOption)) {
      $.extend(this.$options, $extendedOptions[extendedOption]);
    }
  }

  $(this.$calendar).delegate('.fullcalendar-status-close', 'click', function () {
    $(this).parent().slideUp();
    return false;
  });
}

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
Drupal.fullcalendar.registerOptions = function (name, options, dom_id) {
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
Drupal.fullcalendar.fullcalendar.prototype.getOptions = function (dom_id) {
  Drupal.fullcalendar.options[dom_id] = Drupal.fullcalendar.options[dom_id] || {};
  return $.extend({}, Drupal.fullcalendar.options.global, Drupal.fullcalendar.options[dom_id]);
};

Drupal.fullcalendar.update = function (result) {
  var fcStatus = $(result.dom_id).find('.fullcalendar-status');
  if (fcStatus.is(':hidden')) {
    fcStatus.html(result.msg).slideDown();
  }
  else {
    fcStatus.effect('highlight');
  }
  Drupal.attachBehaviors();
  return false;
};

/**
 * Parse Drupal events from the DOM.
 */
Drupal.fullcalendar.parseEvents = function (dom_id, calendar, callback) {
  var events = [];
  var details = $('.fullcalendar-event-details', calendar);
  for (var i = 0; i < details.length; i++) {
    var event = $(details[i]);
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
  }
  callback(events);
};

}(jQuery));
