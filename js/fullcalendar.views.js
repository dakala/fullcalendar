
/**
 * @file
 * Integrates Views data with the FullCalendar plugin.
 */

(function ($) {

Drupal.fullCalendar = Drupal.fullCalendar || {};
Drupal.fullCalendar.navigate = false;
Drupal.fullCalendar.options = {};
Drupal.fullCalendar.droppableCallbacks = {};

Drupal.behaviors.fullCalendar = {
  attach: function(context) {
    // Process each view and its settings.
    $.each(Drupal.settings.fullcalendar, function(index, settings) {
      // Create an object of this calendar.
      var calendar = $(index);

      // Hide the failover display.
      $('.fullcalendar-content', calendar).hide();

      // Fetch timeformat settings.
      var timeformatSettings = settings.timeformat;
      if (settings.advanced) {
        timeformatSettings = {
          month: settings.timeformatMonth,
          week: settings.timeformatWeek,
          day: settings.timeformatDay
        }
      };

      var options = {
        defaultView: settings.defaultView,
        theme: settings.theme,
        header: {
          left: settings.left,
          center: settings.center,
          right: settings.right
        },
        isRTL: settings.isRTL === '1',
        eventClick: function(calEvent, jsEvent, view) {
          // Use colorbox only for events based on entities
          if (settings.colorbox && (calEvent.eid !== undefined)) {
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
          else {
            if (settings.sameWindow) {
              window.open(calEvent.url, '_self');
            }
            else {
              window.open(calEvent.url);
            }
          }
          return false;
        },
        droppable: (settings.droppable === 1),
        drop: function (date, allDay, jsEvent, ui) {
          object = this;
          $.each(Drupal.fullCalendar.droppableCallbacks, function () {
            if ($.isFunction(this.callback)) {
              try {
                this.callback(date, allDay, jsEvent, ui, object);
              }
              catch (exception) {
                alert(exception);
              }
            }
          });
        },
        editable: true,
        year: (settings.year) ? settings.year : undefined,
        month: (settings.month) ? settings.month : undefined,
        date: (settings.day) ? settings.day : undefined,
        timeFormat: timeformatSettings,
        titleFormat: {
          month: settings.titleformatMonth,
          week: settings.titleformatWeek,
          day: settings.titleformatDay
        },
        columnFormat: {
          month: settings.columnformatMonth,
          week: settings.columnformatWeek,
          day: settings.columnformatDay
        },
        axisFormat: (settings.advanced !== 0) ? settings.axisformat : undefined,
        contentHeight: settings.contentHeight,
        weekMode: settings.weekMode,
        firstDay: settings.firstDay,
        monthNames: settings.monthNames,
        monthNamesShort: settings.monthNamesShort,
        dayNames: settings.dayNames,
        dayNamesShort: settings.dayNamesShort,
        allDayText: settings.allDayText,
        buttonText: {
          today:  settings.todayString,
          day: settings.dayString,
          week: settings.weekString,
          month: settings.monthString
        },
        events: function(start, end, callback) {

          // Fetch new items from Views if possible.
          if (Drupal.fullCalendar.navigate && settings.ajax) {

            date = $('.fullcalendar', calendar).fullCalendar('getDate');
            month = $.fullCalendar.formatDate(date, 'MM');
            year = $.fullCalendar.formatDate(date, 'yyyy');
            date_argument = year + settings.separator + month;
            arguments = settings.args.replace('full_calendar_browse', date_argument);
            fetch_url = Drupal.settings.basePath + 'fullcalendar/ajax/results/' + settings.view_name + '/' + settings.view_display + '/' + arguments;

            $.ajax({
              type: 'GET',
              url: fetch_url,
              dataType: 'json',
              beforeSend: function() {
                // Add a throbber.
                this.progress = $('<div class="ajax-progress ajax-progress-throbber"><div class="throbber">&nbsp;</div></div>');
                $(index + ' .fc-header-title').after(this.progress);
              },
              success: function (data) {
                if (data.status) {
                  // Replace content.
                  $(index + ' .fullcalendar-content').html(data.content);
                  Drupal.fullCalendar.ParseEvents(index, calendar, callback);
                }
                // Remove the throbber.
                $(this.progress).remove();
              },
              error: function (xmlhttp) {
                alert(Drupal.t('An HTTP error @status occurred.', {'@status': xmlhttp.status}));
              }
            });
          }
          else {
            Drupal.fullCalendar.ParseEvents(index, calendar, callback);
          }

          if (!Drupal.fullCalendar.navigate) {
            // Add events from Google Calendar feeds.
            $.each(settings.gcal, function(i, gcalEntry) {
              $('.fullcalendar', calendar).fullCalendar('addEventSource',
                $.fullCalendar.gcalFeed(gcalEntry[0], gcalEntry[1])
              );
            });
          }

          // Set navigate to true which means we've starting clicking on
          // next and previous buttons if we re-enter here again.
          Drupal.fullCalendar.navigate = true;

        },
        eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
          $.post(Drupal.settings.basePath + 'fullcalendar/ajax/update/drop/'+ event.eid,
            'field=' + event.field + '&entity_type=' + event.entity_type + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta + '&all_day=' + allDay + '&dom_id=' + event.dom_id,
            fullcalendarUpdate);
          return false;
        },
        eventResize: function(event, dayDelta, minuteDelta, revertFunc) {
          $.post(Drupal.settings.basePath + 'fullcalendar/ajax/update/resize/'+ event.eid,
            'field=' + event.field + '&entity_type=' + event.entity_type + '&index=' + event.index + '&day_delta=' + dayDelta + '&minute_delta=' + minuteDelta + '&dom_id=' + event.dom_id,
            fullcalendarUpdate);
          return false;
        }
      };

      // Allow other modules to overwrite options.
      $.each(Drupal.fullCalendar.options, function() {
        $.extend(options, this);
      });

      // Use .once() to protect against extra AJAX calls from Colorbox.
      $('.fullcalendar', calendar).once().fullCalendar(options);
    });

    var fullcalendarUpdate = function(result) {
      fcStatus = $(result.dom_id).find('.fullcalendar-status');
      if (fcStatus.text() === '') {
        fcStatus.html(result.msg).slideDown();
      }
      else {
        fcStatus.effect('highlight', {}, 5000);
      }
      return false;
    };

    $('.fullcalendar-status-close').live('click', function() {
      $(this).parent().slideUp();
      return false;
    });

    // Trigger a window resize so that calendar will redraw itself as it loads funny in some browsers occasionally
    $(window).resize();
  }
};

/**
 * Parse Drupal events from the DOM.
 */
Drupal.fullCalendar.ParseEvents = function(index, calendar, callback) {
  var events = [];
  // Drupal events.
  $('.fullcalendar-event-details', calendar).each(function() {
    var event = $(this);
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
      dom_id: index
    });
  });
  callback(events);
};

})(jQuery);
