(function($) {

Drupal.fullcalendar.plugins.fullcalendar_options = {
  options: function (fullcalendar) {
    var options = Drupal.settings.fullcalendar[fullcalendar.dom_id].fullcalendar_options;

    if (options.dayClick) {
      options.dayClick = function (date, allDay, jsEvent, view) {
        // No need to change the view if it's already set.
        if (view == options.dayClickView) {
          return;
        }

        $('.fullcalendar', fullcalendar.$calendar)
          .fullCalendar('gotoDate', date)
          .fullCalendar('changeView', options.dayClickView);
      };
    }
    else {
      delete options.dayClick;
    }

    return options;
  }
};

}(jQuery));
