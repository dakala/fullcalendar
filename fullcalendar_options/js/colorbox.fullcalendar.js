(function($) {

Drupal.behaviors.fullcalendar_options_colorbox = {
  attach: function(context, settings) {
    for (var dom_id in settings.fullcalendar) {
      if (settings.fullcalendar.hasOwnProperty(dom_id)) {
        if (settings.fullcalendar[dom_id].colorbox.colorbox) {
          Drupal.fullcalendar.registerOptions(
            'fullcalendar_options_colorbox',
            Drupal.fullcalendar_options_colorbox.processOptions(settings.fullcalendar[dom_id].colorbox),
            dom_id
          );
        }
      }
    }
  }
};

Drupal.fullcalendar_options_colorbox = {};

Drupal.fullcalendar_options_colorbox.processOptions = function(settings) {
  return {
    eventClick: function(calEvent, jsEvent, view) {
      // Use colorbox only for events based on entities
      if (calEvent.eid !== undefined) {
        // Open in colorbox if exists, else open in new window.
        if ($.colorbox) {
          var url = calEvent.url;
          if (settings.colorboxClass !== '') {
            url += ' ' + settings.colorboxClass;
          }
          $.colorbox({
            href: url,
            width: settings.colorboxWidth,
            height: settings.colorboxHeight,
            iframe: settings.colorboxIFrame === 1 ? true : false
          });
        }
      }
      return false;
    }
  };
};

}(jQuery));
