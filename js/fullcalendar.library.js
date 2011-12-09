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
Drupal.fullcalendar.plugins = {};
Drupal.fullcalendar.droppableCallbacks = {};

// Alias old fullCalendar namespace.
Drupal.fullCalendar = Drupal.fullcalendar;

Drupal.fullcalendar.fullcalendar = function (dom_id) {
  this.dom_id = dom_id;
  this.$calendar = $(dom_id);
  this.$options = {};

  // Allow other modules to overwrite options.
  var $options = {};
  for (var $plugin in Drupal.fullcalendar.plugins) {
    if (Drupal.fullcalendar.plugins.hasOwnProperty($plugin)) {
      if (typeof Drupal.fullcalendar.plugins[$plugin].options == 'function') {
        var option = {};
        option[$plugin] = Drupal.fullcalendar.plugins[$plugin].options(this);
        $.extend($options, option);
      }
    }
  }

  // Hide the failover display.
  $('.fullcalendar-content', this.$calendar).hide();

  // Load the base FullCalendar options first.
  // @todo Use the weights system to order this.
  $.extend(this.$options, $options.fullcalendar);
  delete $options.fullcalendar;

  // Loop through additional options, overwriting the defaults.
  for (var option in $options) {
    if ($options.hasOwnProperty(option)) {
      $.extend(this.$options, $options[option]);
    }
  }

  $(this.$calendar).delegate('.fullcalendar-status-close', 'click', function () {
    $(this).parent().slideUp();
    return false;
  });
}

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
