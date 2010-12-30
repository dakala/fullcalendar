// $Id$
(function ($) {

Drupal.behaviors.fullCalendar = function(context) {
  $('#fullcalendar-content').hide(); //hide the failover display
  $('#fullcalendar').fullCalendar({
    defaultView: Drupal.settings.fullcalendar.defaultView,
    theme: Drupal.settings.fullcalendar.theme,
    header: {
      left: Drupal.settings.fullcalendar.left,
      center: Drupal.settings.fullcalendar.center,
      right: Drupal.settings.fullcalendar.right
    },
    eventClick: function(calEvent, jsEvent, view) {
      if (Drupal.settings.fullcalendar.colorbox) {
      // Open in colorbox if exists, else open in new window.
        if ($.colorbox) {
          $.colorbox({href:calEvent.url, iframe:true, width:'80%', height:'80%'});
        } else {
          window.open(calEvent.url);
        }
      } else {
        window.open(calEvent.url);
      }
      return false;
    },
    year: (Drupal.settings.fullcalendar.year) ? Drupal.settings.fullcalendar.year : undefined,
    month: (Drupal.settings.fullcalendar.month) ? Drupal.settings.fullcalendar.month : undefined,
    day: (Drupal.settings.fullcalendar.day) ? Drupal.settings.fullcalendar.day : undefined,
    timeFormat: {
      agenda: (Drupal.settings.fullcalendar.clock) ? 'HH:mm{ - HH:mm}' : Drupal.settings.fullcalendar.agenda,
      '': (Drupal.settings.fullcalendar.clock) ? 'HH:mm' : 'h(:mm)t'
    },
    axisFormat: (Drupal.settings.fullcalendar.clock) ? 'HH:mm' : 'h(:mm)tt',
    weekMode: Drupal.settings.fullcalendar.weekMode,
    firstDay: Drupal.settings.fullcalendar.firstDay,
    events: function(start, end, callback) {
      var events = [];

      $('.fullcalendar_event').each(function() {
        $(this).find('.fullcalendar_event_details').each(function() {
          events.push({
            field: $(this).attr('field'),
            index: $(this).attr('index'),
            nid: $(this).attr('nid'),
            title: $(this).attr('title'),
            start: $(this).attr('start'),
            end: $(this).attr('end'),
            url: $(this).attr('href'),
            allDay: ($(this).attr('allDay') == '1'),
            className: $(this).attr('cn'),
            editable: $(this).attr('editable')
          });
        });
      });

      callback(events);
    },
    eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
      $.post('/fullcalendar/ajax/update/drop/'+ event.nid,
        'field=' + event.field + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta + '&all_day=' + allDay,
        fullcalendarUpdate);
      return false;
    },
    eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
      $.post('/fullcalendar/ajax/update/resize/'+ event.nid,
        'field=' + event.field + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta,
        fullcalendarUpdate);
      return false;
    }
  });

  var fullcalendarUpdate = function(response) {
    var result = Drupal.parseJson(response);
    if ($('#fullcalendar-status').text() == '') {
      $('#fullcalendar-status').html(result.msg).slideDown();
    } else {
      $('#fullcalendar-status').html(result.msg).effect('highlight', {}, 5000);
    }
    return false;
  }

  $('.fullcalendar-status-close').live('click', function() {
    $('#fullcalendar-status').slideUp();
    return false;
  });

  //trigger a window resize so that calendar will redraw itself as it loads funny in some browsers occasionally
  $(window).resize();
};

})(jQuery);
