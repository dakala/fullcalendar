// $Id$

/**
 * @file
 * Integrates Views data with the FullCalendar plugin.
 */

(function ($) {

Drupal.behaviors.fullCalendar = {
attach: function(context) {
  $('#fullcalendar-content').hide(); //hide the failover display
  $('#fullcalendar').once().fullCalendar({
    defaultView: Drupal.settings.fullcalendar.defaultView,
    theme: Drupal.settings.fullcalendar.theme,
    header: {
      left: Drupal.settings.fullcalendar.left,
      center: Drupal.settings.fullcalendar.center,
      right: Drupal.settings.fullcalendar.right
    },
    isRTL: Drupal.settings.fullcalendar.isRTL === '1',
    eventClick: function(calEvent, jsEvent, view) {
      if (Drupal.settings.fullcalendar.colorbox) {
      // Open in colorbox if exists, else open in new window.
        if ($.colorbox) {
          var url = calEvent.url;
          if (Drupal.settings.fullcalendar.colorboxClass !== '') {
            url += ' ' + Drupal.settings.fullcalendar.colorboxClass;
          }
          $.colorbox({
            href: url,
            width: Drupal.settings.fullcalendar.colorboxWidth,
            height: Drupal.settings.fullcalendar.colorboxHeight
          });
        }
      }
      else {
        if (Drupal.settings.fullcalendar.sameWindow) {
          window.open(calEvent.url, _self);
        }
        else {
          window.open(calEvent.url);
        }
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
    monthNames: Drupal.settings.fullcalendar.monthNames,
    monthNamesShort: Drupal.settings.fullcalendar.monthNamesShort,
    dayNames: Drupal.settings.fullcalendar.dayNames,
    dayNamesShort: Drupal.settings.fullcalendar.dayNamesShort,
    allDayText: Drupal.settings.fullcalendar.allDayText,
    buttonText: {
      today:  Drupal.settings.fullcalendar.todayString,
      day: Drupal.settings.fullcalendar.dayString,
      week: Drupal.settings.fullcalendar.weekString,
      month: Drupal.settings.fullcalendar.monthString
    },
    eventSources: [
    function(start, end, callback) {
      var events = [];

      $('.fullcalendar_event').each(function() {
        $(this).find('.fullcalendar_event_details').each(function() {
          events.push({
            field: $(this).attr('field'),
            index: $(this).attr('index'),
            eid: $(this).attr('eid'),
            entity_type: $(this).attr('entity_type'),
            title: $(this).attr('title'),
            start: $(this).attr('start'),
            end: $(this).attr('end'),
            url: $(this).attr('href'),
            allDay: ($(this).attr('allDay') === '1'),
            className: $(this).attr('cn'),
            editable: $(this).attr('editable')
          });
        });
      });

      callback(events);
    },
    $.fullCalendar.gcalFeedArray(Drupal.settings.fullcalendar.gcal)
    ],
    eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
      $.post(Drupal.settings.basePath + 'fullcalendar/ajax/update/drop/'+ event.eid,
        'field=' + event.field + '&entity_type=' + event.entity_type + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta + '&all_day=' + allDay,
        fullcalendarUpdate);
      return false;
    },
    eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
      $.post(Drupal.settings.basePath + 'fullcalendar/ajax/update/resize/'+ event.eid,
        'field=' + event.field + '&entity_type=' + event.entity_type + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta,
        fullcalendarUpdate);
      return false;
    }
  });

  var fullcalendarUpdate = function(result) {
    if ($('#fullcalendar-status').text() === '') {
      $('#fullcalendar-status').html(result.msg).slideDown();
    } else {
      $('#fullcalendar-status').html(result.msg).effect('highlight', {}, 5000);
    }
    return false;
  };

  $('.fullcalendar-status-close').live('click', function() {
    $('#fullcalendar-status').slideUp();
    return false;
  });

  //trigger a window resize so that calendar will redraw itself as it loads funny in some browsers occasionally
  $(window).resize();
}
};

})(jQuery);
