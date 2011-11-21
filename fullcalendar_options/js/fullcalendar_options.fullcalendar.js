(function($) {

Drupal.behaviors.fullcalendar_options = {
  attach: function(context, settings) {
    for (var dom_id in settings.fullcalendar) {
      if (settings.fullcalendar.hasOwnProperty(dom_id)) {
        Drupal.fullcalendar.registerOptions(
          'fullcalendar_options',
          settings.fullcalendar[dom_id].fullcalendar_options,
          dom_id
        );
      }
    }
  }
};

})(jQuery);

