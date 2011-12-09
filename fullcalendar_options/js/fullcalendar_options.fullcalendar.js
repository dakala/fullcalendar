(function($) {

Drupal.fullcalendar.plugins.fullcalendar_options = {
  options: function (fullcalendar) {
    return Drupal.settings.fullcalendar[fullcalendar.dom_id].fullcalendar_options;
  }
};

}(jQuery));
