/**
 * @file
 * Integrates Views data with the FullCalendar plugin.
 */

(function ($) {

Drupal.behaviors.fullcalendar = {
  attach: function (context, settings) {
    // Process each view and its settings.
    for (var dom_id in settings.fullcalendar) {
      if (settings.fullcalendar.hasOwnProperty(dom_id)) {
        // Create a new fullcalendar object.
        var fullcalendar = new Drupal.fullcalendar.fullcalendar(dom_id);
      }
    }

    // Trigger a window resize so that calendar will redraw itself.
    $(window).resize();
  }
};

}(jQuery));
