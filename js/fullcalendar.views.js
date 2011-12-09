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

        // Use .once() to protect against extra AJAX calls from Colorbox.
        $('.fullcalendar', fullcalendar.$calendar).once().fullCalendar(fullcalendar.$options);
      }
    }

    // Trigger a window resize so that calendar will redraw itself.
    $(window).resize();
  }
};

}(jQuery));
