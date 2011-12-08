/**
 * @file
 * Integrates Views data with the FullCalendar plugin.
 */

(function ($) {

Drupal.behaviors.fullcalendar = {
  attach: function (context, settings) {
    var calendar, options, extendedOptions;
    // Process each view and its settings.
    for (var dom_id in settings.fullcalendar) {
      if (!settings.fullcalendar.hasOwnProperty(dom_id)) {
        continue;
      }

      // Create an object of this calendar.
      calendar = $(dom_id);

      // Hide the failover display.
      $('.fullcalendar-content', calendar).hide();

      // Prepare our options.
      options = {};

      // Allow other modules to overwrite options.
      extendedOptions = Drupal.fullcalendar.getOptions(dom_id);

      // Load the base FullCalendar options first.
      // @todo Use the weights system to order this.
      $.extend(options, extendedOptions.fullcalendar);
      delete extendedOptions.fullcalendar;

      // Loop through additional options, overwriting the defaults.
      for (var extendedOption in extendedOptions) {
        if (extendedOptions.hasOwnProperty(extendedOption)) {
          $.extend(options, extendedOptions[extendedOption]);
        }
      }

      // Use .once() to protect against extra AJAX calls from Colorbox.
      $('.fullcalendar', calendar).once().fullCalendar(options);
    }

    $('.fullcalendar-status-close', calendar).live('click', function () {
      $(this).parent().slideUp();
      return false;
    });

    // Trigger a window resize so that calendar will redraw itself.
    $(window).resize();
  }
};

}(jQuery));
